<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\Count;

class CheckoutController extends Controller
{
    public function showCheckoutForm()
    {

        $cart = Cart::with('details')->where('user_id', Auth::id())->first();

        if (!$cart || $cart->details->isEmpty()) {
            return redirect()->route('cart.listCart')->with('error', 'Giỏ hàng trống');
        }

        $payments = Payment::all();
        return view('client.checkOut', compact('cart', 'payments'));
    }
    public function processCheckout(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000',
            'payment_id' => 'nullable|exists:payments,id',
            'cart' => 'required|array',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thanh toán.');
        }

        $cartItems = $request->input('cart');
        $shipping_fee = $request->input('shipping_fee', 0);
        $tax = $request->input('tax', 0);

        // Lấy coupon_id từ session nếu có
        $coupon_id = session('applied_coupon');
        $coupon = $coupon_id ? Coupon::find($coupon_id) : null;

        try {
            DB::beginTransaction();

            $total_price = 0;
            foreach ($cartItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ tồn kho.");
                }
                $total_price += $item['quantity'] * $product->price;
            }

            // Áp dụng giảm giá nếu có coupon hợp lệ
            if ($coupon && $coupon->status === '1') {
                $currentDate = Carbon::now();
                if ($currentDate->between(Carbon::parse($coupon->start_date), Carbon::parse($coupon->end_date))) {
                    if ($coupon->discount_type === 'percentage') {
                        $discount = ($total_price * $coupon->discount_value) / 100;
                        $total_price -= min($discount, $total_price); // Không vượt quá tổng giá
                    } elseif ($coupon->discount_type === 'fixed') {
                        $total_price -= min($coupon->discount_value, $total_price);
                    }
                }
            }

            $total_price += $shipping_fee + $tax;

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $total_price,
                'status' => 'pending',
                'payment_id' => $request->payment_id,
                'coupon_id' => $coupon_id,
                'shipping_address' => $request->shipping_address,
                'shipping_fee' => $shipping_fee,
                'tax' => $tax,
                'note' => $request->note,
            ]);

            foreach ($cartItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total_price' => $item['quantity'] * $product->price,
                ]);
                $product->quantity -= $item['quantity'];
                $product->save();
            }

            $userCart = Cart::where('user_id', $user->id)->first();
            if ($userCart) {
                $userCart->details()->delete();
                $userCart->delete();
            }
 
            DB::commit();

            // Xóa session coupon sau khi áp dụng
            session()->forget('applied_coupon');

            return redirect()->route('cart.listCart')->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi đặt hàng: ' . $e->getMessage());
        }
    }
    public function trackOrder()
    {
        // Lấy danh sách đơn hàng của người dùng hiện tại, kèm chi tiết
        $orders = Order::where('user_id', Auth::id())
            ->with('orderDetails.product') // Load chi tiết đơn hàng và sản phẩm
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.trackOrder', compact('orders'));
    }

    public function cancel(Order $order)
    {
        // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
        if ($order->user_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền hủy đơn hàng này.');
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng ở trạng thái đang chờ xử lý.');
        }

        try {
            DB::beginTransaction();

            // Cập nhật trạng thái đơn hàng thành cancelled
            $order->status = 'cancelled';
            $order->save();

            // Hoàn lại tồn kho cho các sản phẩm trong đơn hàng
            foreach ($order->orderDetails as $detail) {
                $product = $detail->product;
                $product->quantity += $detail->quantity;
                $product->save();
            }

            DB::commit();

            return redirect()->route('checkout.trackOrder')->with('success', 'Đơn hàng đã được hủy thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cancel order error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi hủy đơn hàng: ' . $e->getMessage());
        }
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:255',
            'cart' => 'required|array',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);
    
        $couponCode = $request->coupon_code;
        $currentDate = Carbon::now();
        $cartItems = $request->cart;
    
        $coupon = Coupon::where('code', $couponCode)->first();
        // dd($coupon);
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại.'], 400);
        }
    
        if ($coupon->status !== 1) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không khả dụng.'], 400);
        }
    
        if ($currentDate->lt(Carbon::parse($coupon->start_date)) || $currentDate->gt(Carbon::parse($coupon->end_date))) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không nằm trong thời gian sử dụng.'], 400);
        }
    
        $total_price = 0;
        foreach ($cartItems as $item) {
            $product = Product::findOrFail($item['product_id']);
            $total_price += $item['quantity'] * $product->price;
        }
    
        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($total_price * $coupon->discount_value) / 100;
            $total_price -= min($discount, $total_price);
        } elseif ($coupon->discount_type === 'fixed') {
            $discount = $coupon->discount_value;
            $total_price -= min($discount, $total_price);
        }
    
        session(['applied_coupon' => $coupon->id]);
    
        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'new_total' => number_format($total_price, 0, ',', '.'),
            'discount_amount' => number_format($discount, 0, ',', '.')
        ]);
    }
}
