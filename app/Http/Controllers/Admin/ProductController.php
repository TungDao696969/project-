<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->orderBy('id', 'desc');

        $search = $request->input('search');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%') // Tìm kiếm theo tên sản phẩm
                  ->orWhereHas('category', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%'); // Tìm kiếm theo tên danh mục
                  });
            });
        }

        $products = $query->paginate(10);

        return view('admin.products.list-product', compact('products', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = Category::all();
        return view('admin.products.create-product', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            'category_id.required' => 'Phải chọn danh mục.',
            'category_id.exists' => 'Danh mục không hợp lệ.',

            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là số',
            'price.min' => 'Giá sản phẩm ko được nhỏ hơn 0.',

            'discount_price.numeric' => 'Giá khuyến mãi phải là số.',
            'discount_price.min' => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'discount_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',

            'quantity.required' => 'Số lượng sản phẩm là bắt buộc.',
            'quantity.integer' => 'Số lượng sản phẩm phải là số nguyên.',
            'quantity.min' => 'Số lượng sản phẩm ko được nhỏ hơn 0.',


            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',

            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, hoặc gif.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 2MB.',

            'description.string' => 'Mô tả phải là chuỗi ký tự.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'image' => $imagePath,
            'description' => $request->description,
            // dd($request->name, \Illuminate\Support\Str::slug($request->name)),


        ]);

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $category = Category::all();
        return view('admin.products.detail-product', compact('product', 'category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {

        $category = Category::all();
        return view('admin.products.edit', compact('product', 'category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            'category_id.required' => 'Phải chọn danh mục.',
            'category_id.exists' => 'Danh mục không hợp lệ.',

            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là số',
            'price.min' => 'Giá sản phẩm ko được nhỏ hơn 0.',

            'discount_price.numeric' => 'Giá khuyến mãi phải là số.',
            'discount_price.min' => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'discount_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',

            'quantity.required' => 'Số lượng sản phẩm là bắt buộc.',
            'quantity.integer' => 'Số lượng sản phẩm phải là số nguyên.',
            'quantity.min' => 'Số lượng sản phẩm ko được nhỏ hơn 0.',


            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',

            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, hoặc gif.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 2MB.',

            'description.string' => 'Mô tả phải là chuỗi ký tự.',
        ]);

        $data = $request->except('image');
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // lưu ảnh mới
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }
        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('admin.products.index', compact('product'))->with('success', 'Xóa sản phẩm thành công');
    }

    public function showProduct($id)
    {
        $product = Product::findOrFail($id);

        // Lấy sản phẩm liên quan cùng danh mục
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->take(4)
            ->get();

        return view('client.product-detail', compact('product', 'relatedProducts'));
    }
}
