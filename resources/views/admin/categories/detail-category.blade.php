@extends('admin.layouts.main')

@section('title')
    @parent
    Xem chi tiết danh mục
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5>Chi tiết danh mục</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td>{{ $category->id }}</td>
                        </tr>
                        <tr>
                            <th>Tên danh mục</th>
                            <td>{{ $category->name }}</td>
                        </tr>
                        <tr>
                            <th>Slug</th>
                            <td>{{ $category->slug }}</td>
                        </tr>
                        <tr>
                            <th>Mô tả</th>
                            <td>{{ $category->description }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>{{ $category->status ? 'Hoạt động' : 'Ẩn' }}</td>
                        </tr>
                    </table>
                    <a href="{{ route('admin.categories.listCategory') }}" class="btn btn-theme">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
