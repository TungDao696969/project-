<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    public function listCategory(Request $request) {
        $query = Category::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $listCategory = $query->orderBy('id', 'desc')->paginate(10);

        $listCategory->appends(['search' => $request->search]);

        return view('admin.categories.list-category')->with([
            'listCategory' => $listCategory,
            'search' => $request->search 
        ]);
    }

    public function addCategory() {
        $categories = Category::all();
        return view('admin.categories.add-category', compact('categories'));
    }

    public function addPostCategory(Request $req) {
        $req->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
        ]);

        $data = [
            'name' => $req->name,
            'slug' => \Illuminate\Support\Str::slug($req->name),
            'description' => $req->description,
            'parent_id' => $req->parent_id,
            'status' => $req->status,
        ];
        Category::create($data);
        return redirect()->route('admin.categories.listCategory')->with([
            'message' => 'Thêm mới dữ liệu thành công'
        ]);
    }

    public function deleteCategory($id) {
        $category = Category::findOrFail($id); // Tìm danh mục theo ID
        $category->delete(); // Xóa danh mục

        return redirect()->route('admin.categories.listCategory')->with('message', 'Danh mục đã được xóa thành công!');
    }

    public function detailCategory($id){
        $category = Category::where('id', $id)->first();
        return view('admin.categories.detail-category')->with([
            'category' => $category
        ]);
    }

    public function updateCategory($id) {

        $category = Category::findOrFail($id); // Lấy danh mục cần chỉnh sửa
        $categories = Category::where('id', '!=', $id)->get(); // Lấy danh sách danh mục khác

        return view('admin.categories.update-category', compact('category', 'categories'));
    }

    public function updatePatchCategory($id, Request $req) {
        $req->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
        ]);

        $data = [
            'name' => $req->name,
            'slug' => \Illuminate\Support\Str::slug($req->name),
            'description' => $req->description,
            'parent_id' => $req->parent_id,
            'status' => $req->status,
        ];
        Category::where('id', $id)->update($data);
        return redirect()->route('admin.categories.listCategory')->with([
            'message' => 'Sửa dữ liệu thành công'
        ]);
    }
}
