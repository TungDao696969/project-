@extends('client.layouts.main')

@section('content')
    <!-- order-tracking-area start -->
    <section class="order-tracking-area pt-80 pb-85">
        <div class="container">
            <h2 class="mb-30">Theo dõi đơn hàng</h2>

            <!-- Hiển thị thông báo -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if ($orders->isEmpty())
                <div class="alert alert-info">
                    Bạn chưa có đơn hàng nào.
                </div>
            @else
                <div class="order-list">
                    @foreach ($orders as $order)
                        <div class="order-item mb-20">
                            <div class="order-header">
                                <h4>Đơn hàng #{{ $order->id }} - {{ $order->created_at->format('d/m/Y H:i') }}</h4>
                                <span class="order-status {{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="order-details">
                                <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
                                <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price, 0, ',', '.') }} $</p>
                                <p><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có' }}</p>
                                <h5>Sản phẩm:</h5>
                                <ul>
                                    @foreach ($order->orderDetails as $detail)
                                        <li>
                                            {{ $detail->product->name }} x {{ $detail->quantity }} - 
                                            {{ number_format($detail->total_price, 0, ',', '.') }} $
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="order-actions">
                                @if ($order->status === 'pending')
                                    <form action="{{ route('checkout.cancel', $order->id) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Hủy đơn hàng</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    <!-- order-tracking-area end -->
        @push('styles')
    <style>

        .order-item {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .order-status {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }
        .order-status.pending {
            background-color: #f0ad4e;
        }
        .order-status.cancelled {
            background-color: #d9534f;
        }
        .order-status.completed {
            background-color: #5cb85c;
        }
        .order-details {
            margin-bottom: 15px;
        }
        .order-details ul {
            list-style-type: none;
            padding: 0;
        }
        .order-details li {
            margin-bottom: 5px;
        }
        .order-actions {
            text-align: right;
        }
        .btn-danger {
            background-color: #d9534f;
            border: none;
            padding: 8px 15px;
            color: white;
            border-radius: 5px;
        }
        .btn-danger:hover {
            background-color: #c9302c;
        }
    </style>
    @endpush
@endsection