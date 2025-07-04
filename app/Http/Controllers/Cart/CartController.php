<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartDetail;
class CartController extends Controller
{

    public function listCart() {
        $cart = Cart::with('details.product')->where('user_id', Auth::id())->first();

        // Nếu không có giỏ hàng hoặc giỏ hàng trống
        if (!$cart || $cart->details->isEmpty()) {
            return view('client.cart.list-cart', [
                'cart' => null,
                'isEmpty' => true
            ])->with('message', 'Giỏ hàng của bạn đang trống');
        }

        $subtotal = $cart->details->sum(function ($detail) {
            return $detail->price * $detail->quantity;
        });

        return view('client.cart.list-cart', [
            'cart' => $cart,
            'isEmpty' => false,
            'subtotal' => $subtotal
        ]);
        // return view('client.cart.list-cart', compact('cart'));
    }
     public function addToCart(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1'
    ]);

    $product = Product::findOrFail($validated['product_id']);
    $user_id = Auth::id();

    // Kiểm tra tồn kho hiện tại
    if ($validated['quantity'] > $product->quantity) {
        return redirect()->back()
            ->withErrors(['quantity' => 'Số lượng vượt quá tồn kho hiện tại: ' . $product->quantity])
            ->withInput();
    }

    // Tìm hoặc tạo giỏ hàng
    $cart = Cart::firstOrCreate(
        ['user_id' => $user_id],
        ['total_price' => 0]
    );

    // Kiểm tra sản phẩm đã có trong giỏ chưa
    $cartDetail = CartDetail::where('cart_id', $cart->id)
        ->where('product_id', $product->id)
        ->first();

    if ($cartDetail) {
        $newQuantity = $cartDetail->quantity + $validated['quantity'];

        // Kiểm tra nếu cộng thêm vượt quá tồn kho
        if ($newQuantity > $product->quantity) {
            return redirect()->back()
                ->withErrors(['quantity' => 'Tổng số lượng trong giỏ vượt quá tồn kho: ' . $product->quantity])
                ->withInput();
        }

        $cartDetail->quantity = $newQuantity;
        $cartDetail->total_price = $newQuantity * $product->price;
        $cartDetail->save();
    } else {
        // Thêm mới nếu chưa có
        CartDetail::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
            'price' => $product->price,
            'total_price' => $validated['quantity'] * $product->price,
        ]);
    }

    // Cập nhật tổng giỏ hàng
    $cart->refresh();
    $cart->total_price = $cart->details->sum('total_price');
    $cart->save();

    return redirect()->route('cart.listCart')->with('success', 'Đã thêm sản phẩm vào giỏ hàng');
}
public function remove($id) {
        try {
            // Tìm chi tiết giỏ hàng
            $cartDetail = CartDetail::with('cart')->find($id);
            
            // Kiểm tra nếu không tìm thấy chi tiết giỏ hàng
            if (!$cartDetail) {
                return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng');
            }
            
            // Kiểm tra xem giỏ hàng có thuộc về người dùng hiện tại không
            if ($cartDetail->cart->user_id != Auth::id()) {
                return redirect()->back()->with('error', 'Bạn không có quyền xóa sản phẩm này');
            }
            
            // Lưu lại cart_id để cập nhật tổng giá
            $cartId = $cartDetail->cart_id;
            
            // Xóa chi tiết giỏ hàng
            $cartDetail->delete();
            
            // Cập nhật tổng giá giỏ hàng
            $cart = Cart::find($cartId);
            if ($cart) {
                $cart->total_price = $cart->details->sum('total_price');
                $cart->save();
            }
            
            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    
}
