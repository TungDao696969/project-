@extends('admin.layouts.main')

@section('title')
    @parent
    Danh sách sản phẩm
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Thông báo -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-primary" role="alert">
                {{ session('message') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title d-flex justify-content-between align-items-center">
                            <h5>Danh sách sản phẩm</h5>
                            <div class="d-flex gap-2">
                                <!-- Search Form -->
                                <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex">
                                    <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm theo tên sản phẩm hoặc danh mục..." value="{{ $search ?? '' }}">
                                    <button type="submit" class="btn btn-primary">Tìm</button>
                                    @if ($search)
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Xóa bộ lọc</a>
                                    @endif
                                </form>
                                <!-- Add New Button -->
                                <a href="{{ route('admin.products.create') }}" class="align-items-center btn btn-theme d-flex">
                                    <i data-feather="plus-square"></i>Thêm mới
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive product-table">
                            <div>
                                <table class="table all-package theme-table" id="table_id">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Danh mục</th>
                                            {{-- <th>Slug</th> --}}
                                            <th>Giá</th>
                                            <th>Giảm giá</th>
                                            <th>Số lượng</th>
                                            <th>Ảnh</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($products as $pro)
                                            <tr>
                                                <td>{{ $pro->id }}</td>
                                                <td>{{ $pro->name }}</td>
                                                <td>{{ $pro->category->name ?? 'Không có danh mục' }}</td>
                                                {{-- <td>{{ $pro->slug }}</td> --}}
                                                <td>${{ number_format($pro->price, 2) }}</td>
                                                <td>{{ $pro->discount_price ? number_format($pro->discount_price, 2) . ' $' : 'Không có' }}</td>
                                                <td>{{ $pro->quantity }}</td>
                                                <td>
                                                    @if ($pro->image)
                                                        <img src="{{ asset('storage/' . $pro->image) }}" width="100px" height="100px" alt="Ảnh sản phẩm">
                                                    @else
                                                        <span class="text-muted">Không có ảnh</span>
                                                    @endif
                                                </td>
                                                <td>{{ $pro->status ? 'Hiển thị' : 'Ẩn' }}</td>
                                                <td>
                                                    <ul>
                                                        <li>
                                                            <a href="{{route('admin.products.show', $pro->id)}}">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{route('admin.products.edit', $pro->id)}}">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{$pro->id }}">
                                                                <i class="ri-delete-bin-line text-danger"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <!-- Modal Xóa -->
                                            <div class="modal fade" id="deleteModal{{$pro->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{$pro->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="deleteModalLabel{{ $pro->id }}">Cảnh báo</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-danger">Bạn có chắc chắn muốn xóa sản phẩm <strong>{{$pro->name }}</strong> không?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <form action="{{ route('admin.products.destroy', ['product' =>$pro->id]) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Không có sản phẩm nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-3">
                            @for ($i = 1; $i <= $products->lastPage(); $i++)
                                <a href="{{ $products->url($i) }}" class="mx-1 {{ $i == $products->currentPage() ? 'fw-bold text-primary' : '' }}">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
