<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request){
        $query = Product::with('category')->where('status', 1);
        if($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if($request->has('price') && !empty($request->price)) {
            $effectivePrice = 'coalesce(discount_price, price )';
            switch ($request->price) {
                case 'under-5':
                    $query->whereRaw("$effectivePrice < 5");
                    break;
                case '5-10':
                    $query->whereRaw("$effectivePrice BETWEEN 5 AND 10");
                    break;
                case '10-20':
                    $query->whereRaw("$effectivePrice BETWEEN 10.01 AND 20");
                    break;
                case 'above-20':
                    $query->whereRaw("$effectivePrice > 20");
                    break;
                case 'high-to-low':
                    $query->orderByRaw("$effectivePrice desc");
                    break;
                case 'low-to-high':
                    $query->orderByRaw("$effectivePrice asc");
                    break;
            }
        }
        $products = $query->orderBy('id','desc')->paginate(10);
        return view('client.home', compact('products'));
    }
}
