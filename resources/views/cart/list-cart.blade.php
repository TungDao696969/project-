@extends('client.layouts.main')
@push('styles')
    <style>
        .xc-product-quantity {
            margin-left: 50px;
        }

        .xc-product-quantity {
            margin-left: 50px;
            position: relative;
        }

        .xc-product-quantity .text-danger {
            font-size: 13px;
            color: #dc3545;
            position: absolute;
            bottom: -20px;
            left: 0;
            white-space: nowrap;

        }
    </style>
@endpush
@section('content')
    <div class="xc-cart-page pt-80 pb-80">
        <div class="container">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row gutter-y-30 gx-5">
                <div class="col-lg-8 col-xl-9">
                    <div class="xc-cart-page__table">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="product-thumbnail">Images</th>
                                    <th class="cart-product-name">Product</th>
                                    <th class="product-price">Unit Price</th>
                                    <th class="product-quantity">Quantity</th>
                                    <th class="product-subtotal">Total</th>
                                    <th class="product-remove">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($cart && $cart->details->count() > 0)
                                    @foreach ($cart ? $cart->details : [] as $detail)
                                        <tr>
                                            <td><img src="{{ asset('storage/' . $detail->product->image) }}" width="50">
                                            </td>
                                            <td>{{ $detail->product->name }}</td>
                                            <td>{{ number_format($detail->price, 0, ',', '.') }} $</td>
                                            <td class="product-quantity">
                                                <div class="xc-product-quantity mt-10 mb-10">
                                                    <span class="xc-cart-minus sub" data-detail-id="{{ $detail->id }}">
                                                        <i class="fas fa-minus"></i>
                                                    </span>
                                                    <input class="xc-cart-input" type="text"
                                                        value="{{ $detail->quantity }}" min="1"
                                                        data-detail-id="{{ $detail->id }}"
                                                        data-price="{{ $detail->price }}"
                                                        data-max="{{ $detail->product->quantity }}">
                                                    <span class="xc-cart-plus add" data-detail-id="{{ $detail->id }}">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </div>
                                            </td>

                                            <td id="total-{{ $detail->id }}">
                                                {{ number_format($detail->total_price, 0, ',', '.') }} $</td>
                                            <td>
                                                <form action="{{ route('cart.remove', $detail->id) }}" method="POST"
                                                    class="d-inline" id="delete-form-{{ $detail->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm delete-btn">
                                                        <i class="fas fa-trash-alt"></i> Xóa
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <h4>Giỏ hàng của bạn đang trống</h4>
                                            <a href="{{ route('client.home') }}" class="btn btn-primary mt-3">
                                                <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="shop_cart_widget xc-accordion">
                        <div class="accordion" id="shop_size">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="size__widget">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#size_widget_collapse" aria-expanded="true"
                                        aria-controls="size_widget_collapse">Shopping Cart
                                    </button>
                                </h2>
                                <div id="size_widget_collapse" class="accordion-collapse collapse show"
                                    aria-labelledby="size__widget" data-bs-parent="#shop_size" style="">
                                    <div class="accordion-body">
                                        <div class="cart-coupon-code">
                                            <input type="text" placeholder="Coupon Code">
                                            <button>Apply</button>
                                        </div>
                                        <div class="cart-subtitle">
                                            <h4>Subtotal</h4>
                                            <h4 class="subtotal-amount">{{ number_format($subtotal ?? 0, 0, ',', '.') }} $
                                            </h4>
                                        </div>
                                        {{-- <div class="cart-checkout">
                                            <h4>Shipping</h4>
                                            <div class="shop__widget-list">
                                                <div class="shop__widget-list-item-2">
                                                    <input type="radio" name="pay" id="c-rate">
                                                    <label for="c-rate">Flat rate</label>
                                                </div>
                                                <div class="shop__widget-list-item-2 has-orange">
                                                    <input type="radio" name="pay" id="c-Free">
                                                    <label for="c-Free">Free shipping</label>
                                                </div>
                                                <div class="shop__widget-list-item-2 has-green">
                                                    <input type="radio" name="pay" id="c-pickup">
                                                    <label for="c-pickup">Local pickup</label>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="cart-totails">
                                            <h4>Subtotal</h4>
                                            <h4 class="subtotal-amount">{{ number_format($subtotal ?? 0, 0, ',', '.') }} $
                                            </h4>
                                        </div>
                                        <p>Wetters, as opposed to using Content here, content here, making it look like
                                            readable English. Many desktop </p>
                                        <a class="cart-checkout-btn" href="{{ route('checkout.showCheckoutForm') }}">Checkout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === XÓA SẢN PHẨM ===
            document.querySelectorAll('.delete-btn').forEach(button => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);

                newButton.addEventListener('click', function(e) {
                    if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                        this.closest('form').submit();
                    }
                });
            });

            // === HÀM CẬP NHẬT SUBTOTAL ===
            function updateSubtotal() {
                let subtotal = 0;
                document.querySelectorAll('.xc-cart-input').forEach(input => {
                    const quantity = parseInt(input.value);
                    const price = parseInt(input.dataset.price);
                    subtotal += quantity * price;
                });

                const subtotalElement = document.querySelector('#subtotal-amount');
                if (subtotalElement) {
                    subtotalElement.innerText = subtotal.toLocaleString('vi-VN') + ' $';
                }
            }

            // === HÀM XỬ LÝ TĂNG/GIẢM ===
            function handleQuantityChange(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const button = e.currentTarget;
                const detailId = button.dataset.detailId;
                const input = document.querySelector(`.xc-cart-input[data-detail-id="${detailId}"]`);
                let quantity = parseInt(input.value);
                const unitPrice = parseInt(input.dataset.price);
                const maxQuantity = parseInt(input.dataset.max);

                let errorDiv = document.querySelector(`#error-${detailId}`);
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.id = `error-${detailId}`;
                    errorDiv.classList.add('text-danger', 'mt-1');
                    input.parentNode.appendChild(errorDiv);
                }

                errorDiv.innerText = ''; // Clear lỗi cũ

                if (button.classList.contains('xc-cart-plus')) {
                    if (quantity < maxQuantity) {
                        quantity += 1;
                    } else {
                        errorDiv.innerText = 'Vượt quá số lượng tồn kho';
                        return;
                    }
                } else if (button.classList.contains('xc-cart-minus') && quantity > 1) {
                    quantity -= 1;
                }

                input.value = quantity;

                // Cập nhật tổng từng sản phẩm
                const totalElement = document.getElementById(`total-${detailId}`);
                const totalPrice = quantity * unitPrice;
                totalElement.innerText = totalPrice.toLocaleString('vi-VN') + ' $';

                // Cập nhật tổng Subtotal
                let newSubtotal = 0;
                document.querySelectorAll('.xc-cart-input').forEach(input => {
                    const price = parseInt(input.dataset.price);
                    const qty = parseInt(input.value);
                    newSubtotal += price * qty;
                });

                // Cập nhật tất cả nơi hiển thị Subtotal
                document.querySelectorAll('.subtotal-amount').forEach(el => {
                    el.innerText = newSubtotal.toLocaleString('vi-VN') + ' $';
                });
            }


            // === GẮN SỰ KIỆN CHO NÚT + / - ===
            document.querySelectorAll('.xc-cart-plus, .xc-cart-minus').forEach(button => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);

                newButton.addEventListener('click', handleQuantityChange);
            });

            // Khởi động: cập nhật subtotal nếu cần
            updateSubtotal();
        });
    </script>
@endpush
