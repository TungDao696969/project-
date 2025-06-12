@extends('admin.layouts.main')

@section('content')
    <div class="container">
        <h2>Sửa Sản Phẩm </h2>

        <!-- Hiển thị lỗi validation -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{route('admin.products.update', $product->id)}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- Tên sản phẩm -->
            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}" >
            </div>



            <div class="mb-3">
                <label for="category_id" class="form-label">Danh mục</label>
                <select name="category_id" id="category_id" class="form-control">
                    @foreach ($category as $cate)
                        <option value="{{ $cate->id }}" {{ $product->category_id == $cate->id ? 'selected' : '' }}>
                            {{ $cate->name }}
                        </option>
                    @endforeach
                </select>

            </div>

            <!-- Giá sản phẩm -->
            <div class="mb-3">
                <label for="price" class="form-label">Giá ($)</label>
                <input type="number" name="price" id="price" class="form-control" value="{{ $product->price }}" >
            </div>

            <!-- Giá khuyến mãi -->
            <div class="mb-3">
                <label for="discount_price" class="form-label">Giá khuyến mãi (nếu có)</label>
                <input type="number" name="discount_price" id="discount_price" class="form-control"
                value="{{ $product->discount_price }}">
            </div>

            <!-- Số lượng -->
            <div class="mb-3">
                <label for="quantity" class="form-label">Số lượng</label>
                <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $product->quantity }}"
                    >
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả sản phẩm</label>
                <textarea name="description" id="description" class="form-control"
                    rows="5">{{ $product->description }}</textarea>
            </div>

            <!-- Trạng thái -->
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-control" >
                    <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Ẩn</option>
                </select>
            </div>

            <!-- Ảnh sản phẩm -->
            <div class="mb-3">
                <label for="image" class="form-label">Ảnh sản phẩm</label>
                <input type="file" name="image" id="image" class="form-control">
                @if ($product->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $product->image) }}" width="100px" height="100px" alt="Ảnh sản phẩm">
                    </div>
                @endif
            </div>

            <!-- Nút Submit -->
            <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
        </form>
    </div>
@endsection
