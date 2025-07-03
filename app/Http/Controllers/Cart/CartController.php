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




    
}
