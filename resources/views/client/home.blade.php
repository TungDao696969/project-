@extends('client.layouts.main')
 


@section('content')

    {{-- Banner --}}
    <div class="xc-banner-one pt-20 pb-40">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-xxl-2 d-none d-xl-block">
                    <div class="xc-banner-one__cat">
                        <ul>
                            <li><a href="#">Fashion & Accessories</a></li>
                            <li><a href="#">Sports & Entertainment</a></li>
                            <li><a href="#">Health & Beauty</a></li>
                            <li><a href="#">Digital & Electronics</a></li>
                            <li><a href="#">Tools, Equipment</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-xl-9 col-xxl-9">
                    <div id="carouselExample" class="carousel slide">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="{{ asset('build/client/assets/img/banner/banner10.jpg') }}" class="d-block w-100"
                                    alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('build/client/assets/img/banner/banner11.jpg') }}" class="d-block w-100"
                                    alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('build/client/assets/img/banner/banner12.jpg') }}" class="d-block w-100"
                                    alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('build/client/assets/img/banner/banner14.jpg') }}" class="d-block w-100"
                                    alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('build/client/assets/img/banner/banner10.jpg') }}" class="d-block w-100"
                                    alt="...">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- product two start -->
    <div class="xc-product-two pb-80">
        <div class="container">
            <div class="xc-sec-heading">
                <h3 class="xc-sec-heading__title"><span><i class="icon-power"></i></span>Recommended Items</h3>
            </div>
            <div class="row gutter-y-20 row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-5">
                @foreach ($products as $product)
                    <div class="col">
                        <div class="xc-product-two__item">
                            @if ($product->is_best_deal)
                                <span class="xc-product-two__deal">BEST DEALS</span>
                            @endif
                            <div class="xc-product-two__img">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                            </div>
                            @if ($product->reviews_count > 0)
                                <div class="xc-product-two__ratting">
                                    @for ($i = 0; $i < 5; $i++)
                                        <i class="icon-star {{ $i < $product->rating ? 'filled' : '' }}"></i>
                                    @endfor
                                    ({{ $product->reviews_count }})
                                </div>
                            @endif
                            <h3 class="xc-product-two__title"><a href="{{ route('client.showProduct', $product->id) }}">{{ $product->name }}</a></h3>
                            <h4 class="xc-product-two__price">
                                @if ($product->discount_price)
                                    <span class="text-danger fw-bold">${{ number_format($product->discount_price, 2) }}</span>
                                    <del class="text-muted ms-2">${{ number_format($product->price, 2) }}</del>
                                    <span class="badge bg-success ms-2">-{{ round(100 - ($product->discount_price / $product->price * 100)) }}%</span>
                                @else
                                    <span>${{ number_format($product->price, 2) }}</span>
                                @endif
                            </h4>
                            <div class="xc-product-two__btn">
                                <a href="{{ route('client.showProduct', $product->id) }}"><i class="fas fa-eye"></i></a>
                                <a href="#" onclick="addToCart({{ $product->id }})">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-3">
                @for ($i = 1; $i <= $products->lastPage(); $i++)
                    <a href="{{ $products->url($i) }}" class="mx-1 {{ $i == $products->currentPage() ? 'fw-bold text-primary' : '' }}">
                        {{ $i }}
                    </a>
                @endfor
            </div>
        </div>
    </div>
    <!-- product two end -->

    <!-- top sale product start -->
    {{-- Phần này giữ nguyên vì đang bị comment --}}
    {{-- <div class="xc-product-four pb-80"> ... </div> --}}
@endsection

@push('scripts')
<script>
    function addToCart(productId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('cart.addToCart') }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const productIdInput = document.createElement('input');
        productIdInput.type = 'hidden';
        productIdInput.name = 'product_id';
        productIdInput.value = productId;
        form.appendChild(productIdInput);

        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = 'quantity';
        quantityInput.value = 1;
        form.appendChild(quantityInput);

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
