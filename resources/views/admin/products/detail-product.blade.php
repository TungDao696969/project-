@extends('admin.layouts.main')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5>Chi tiết sản phẩm</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">

            <tr>
                <th>Tên sản phẩm</th>
                <td>{{ $product->name }}</td>
            </tr>

            <tr>
                <th>Slug</th>
                <td>{{ $product->slug }}</td>
            </tr>

            <tr>
                <th>Danh mục</th>
            <td>{{ $product->category->name ?? 'Không có' }}</td>
            </tr>

            <tr>
                <th>Giá</th>
            <td>${{ number_format($product->price, 0, ',', '.') }} </td>
            </tr>

            <tr>
                <th>Giá khuyến mãi</th>
                <td>{{ $product->discount_price ? number_format($product->discount_price, 0, ',', '.') . ' $' : 'Không có' }}</td>
            </tr>

            <tr>
                <th>Số lượng</th>
            <td>{{ $product->quantity }}</td>
            </tr>

            <tr>
                <th>Mô tả</th>
                <td>{{ $product->description }}</td>
            </tr>

            <tr>
                <th>Trạng thái</th>
                <td>{{ $product->status ? 'Hoạt động' : 'Ẩn' }}</td>
            </tr>

            <tr>
                <th>Ảnh sản phẩm</th>
            <td>
                @if ($product->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $product->image) }}" width="150px" height="150px" alt="Ảnh sản phẩm">
                </div>
            @else
                <p>Không có ảnh</p>
            @endif
            </td>
            </tr>

        </div>
    </table>
    <a href="{{ route('admin.products.index') }}" class="btn btn-theme">Quay lại</a>
</div>
</div>
</div>
</div>
</div>
@endsection
