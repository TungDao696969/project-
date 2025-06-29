@extends('client.layouts.main')

@push('styles')
    <style>
        .xc-product-eight__img {
            width: 100%;
            height: 200px;
            /* Chiều cao cố định */
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .xc-product-eight__img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Cắt ảnh để lấp đầy khung */
        }
    </style>
@endpush
@section('content')
    <div class="xc-breadcrumb__area base-bg">
        <div class="xc-breadcrumb__bg w-img xc-breadcrumb__overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="xc-breadcrumb__content p-relative z-index-1">
                        <div class="xc-breadcrumb__list">
                            <span><a href="{{ route('client.home') }}">Home</a></span>
                            <span class="dvdr"><i class="icon-arrow-right"></i></span>
                            <span>Product Details</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chi tiết sản phẩm --}}
    <section class="product__details-area pt-80 pb-80">
        <div class="container">
            <div class="row gutter-y-30">
                <div class="col-xl-6 col-lg-6">
                    <div class="product__details-thumb-tab">
                        <div class="product__details-thumb-content w-img">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6">
                    <div class="product__details-wrapper">
                        <div class="product__details-stock" data-stock="{{ $product->quantity }}">
                            <span>{{ $product->quantity }} In Stock</span>
                        </div>
                        <h3 class="product__details-title">{{ $product->name }}</h3>

                        @if ($product->reviews_count > 0)
                            <div class="product__details-rating">
                                @for ($i = 0; $i < 5; $i++)
                                    <i class="icon-star {{ $i < $product->rating ? 'filled' : '' }}"></i>
                                @endfor
                                <span>({{ $product->reviews_count }} reviews)</span>
                            </div>
                        @endif

                        <p>{{ $product->description }}</p>

                        <div class="product__details-price">
                            @if ($product->discount_price)
                                <!-- Giá gốc và giá giảm -->
                                <span class="product__details-ammount old-ammount" data-unitprice="{{ $product->price }}">
                                    $<span class="price-old">{{ number_format($product->price, 2) }}</span>
                                </span>
                                <span class="product__details-ammount new-ammount"
                                    data-unitprice="{{ $product->discount_price }}" data-discount="true">
                                    $<span class="price-current">{{ number_format($product->discount_price, 2) }}</span>
                                </span>
                            @else
                                <span class="product__details-ammount new-ammount" data-unitprice="{{ $product->price }}">
                                    $<span class="price-current">{{ number_format($product->price, 2) }}</span>
                                </span>
                            @endif
                        </div>

                        <div class="product__details-action d-flex flex-wrap align-items-center">
                            <form action="{{ route('cart.addToCart') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" class="quantity-input" value="1">

                                <div class="xc-product-quantity mt-10 mb-10">
                                    <span class="xc-cart-minus">
                                        <i class="fas fa-minus"></i>
                                    </span>
                                    <input class="xc-cart-input" type="text" value="1" readonly>
                                    <span class="xc-cart-plus">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                </div>
                                <div class="quantity-error text-danger" style="display: none; font-weight: bold;">
                                    Vượt quá số lượng tồn kho
                                </div>

                                <button type="submit" class="product-add-cart-btn swiftcart-btn">
                                    Add to cart
                                </button>
                            </form>
                        </div>

                        <div class="product__details-share">
                            <span>Category:</span>

                            <span><a href="#">{{ $product->category->name }}</a></span>
                        </div>

                        <div class="product__details-share">
                            <span>Share:</span>

                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#"><i class="fa-brands fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Sản phẩm liên quan --}}
    <div class="xc-related-product pb-80">
        <div class="container">
            <h3 class="xc-section-title mb-30">Related Products</h3>
            <div class="row gutter-y-30">
                @foreach ($relatedProducts as $related)
                    <div class="col-xl-3 col-md-6">
                        <div class="xc-product-eight__item">
                            <div class="xc-product-eight__img">
                                <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->name }}">
                                <span class="xc-product-eight__offer">-{{ rand(5, 30) }}% off</span>
                            </div>
                            <div class="xc-product-eight__content">
                                <h3 class="xc-product-eight__title">
                                    <a href="{{ route('client.showProduct', $product->id) }}">{{ $related->name }}</a>
                                </h3>
                                <h5 class="xc-product-eight__price">${{ number_format($related->price, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const minusBtns = document.querySelectorAll('.xc-cart-minus');
        const plusBtns = document.querySelectorAll('.xc-cart-plus');
        const quantityInputs = document.querySelectorAll('.xc-cart-input');

        const currentPriceElement = document.querySelector('.price-current');
        const oldPriceElement = document.querySelector('.price-old');

        let unitPrice = 0;
        let oldUnitPrice = 0;

        const newAmountElement = document.querySelector('.new-ammount');
        if (newAmountElement.dataset.discount === 'true') {
            unitPrice = parseFloat(newAmountElement.dataset.unitprice);
            oldUnitPrice = parseFloat(document.querySelector('.old-ammount').dataset.unitprice);
        } else {
            unitPrice = parseFloat(newAmountElement.dataset.unitprice);
        }

        const stock = parseInt(document.querySelector('.product__details-stock').dataset.stock);

        // Tăng số lượng
        plusBtns.forEach(function (button) {
            button.addEventListener('click', function () {
                const wrapper = this.closest('.xc-product-quantity');
                const input = wrapper.querySelector('.xc-cart-input');
                const form = this.closest('form');
                const errorMessage = form.querySelector('.quantity-error');

                if (form) {
                    const quantityInput = form.querySelector('.quantity-input');
                    let currentValue = parseInt(input.value);

                    if (currentValue < stock) {
                        currentValue += 1;
                        input.value = currentValue;
                        quantityInput.value = currentValue;

                        if (errorMessage) errorMessage.style.display = 'none';

                        updatePrice(currentValue);
                    } else {
                        if (errorMessage) errorMessage.style.display = 'block';
                    }
                }
            });
        });

        // Giảm số lượng
        minusBtns.forEach(function (button) {
            button.addEventListener('click', function () {
                const wrapper = this.closest('.xc-product-quantity');
                const input = wrapper.querySelector('.xc-cart-input');
                const form = this.closest('form');
                const errorMessage = form.querySelector('.quantity-error');

                if (form) {
                    const quantityInput = form.querySelector('.quantity-input');
                    let currentValue = parseInt(input.value);

                    if (currentValue > 1) {
                        currentValue -= 1;
                        input.value = currentValue;
                        quantityInput.value = currentValue;

                        if (errorMessage) errorMessage.style.display = 'none';

                        updatePrice(currentValue);
                    }
                }
            });
        });

        // Cập nhật giá
        function updatePrice(quantity) {
            if (currentPriceElement) {
                const totalPrice = (quantity * unitPrice).toFixed(2);
                currentPriceElement.textContent = formatNumber(totalPrice);
            }

            if (oldPriceElement && oldUnitPrice) {
                const oldTotalPrice = (quantity * oldUnitPrice).toFixed(2);
                oldPriceElement.textContent = formatNumber(oldTotalPrice);
            }
        }

        // Định dạng số
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    });
</script>
@endpush
