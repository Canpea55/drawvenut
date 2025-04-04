@extends('layouts.app')

@section('style')
@endsection

@section('foot-script')
@endsection

@section('subnavbar-right') 
        @if (auth()->check())
            @if (session('cart'))
                @php
                    // Get count of all option in cart
                    $optionCount = 0;
                    $cart = session()->get('cart', []);
                    foreach($cart as $product){
                        $optionCount += count($product['selectedOptions']);
                    }
                @endphp
                <a class="animU no-underline fw-light bg-success-subtle rounded px-2 py-1" href="{{route('cart')}}">
                    <!-- This div is visible on large screens (PC) but hidden on small screens (phones) -->
                    <div class="d-none d-md-block">
                        <img src="{{ asset('icons/oval-interface/shopping-bag.svg') }}" alt="Cart Icon" width="24">
                        <span class="align-middle"> 
                            ตะกร้าสินค้า {{ "(" . $optionCount . ")" }}
                        </span>
                    </div>
                    <!-- This div is visible on small screens (phones) but hidden on larger screens -->
                    <div class="d-md-none">
                        <img src="{{ asset('icons/oval-interface/shopping-bag.svg') }}" alt="Cart Icon" width="24">
                        <div class="position-relative d-inline-block">
                            <span class="position-absolute translate-middle">
                                {{ $optionCount }}
                            </span>
                        </div>
                    </div>
                </a>
            @endif

            <a id="favorites-link" class="animU no-underline fw-light bg-danger-subtle rounded px-2 py-1" style="display: none" href="{{route('favorites')}}">
                <!-- Desktop -->
                <div class="d-none d-md-block">
                    <img src="{{ asset('icons/oval-interface/heart.svg') }}" alt="Heart Icon" width="24">
                    <span id="favorites-count" class="align-middle">
                        สิ่งที่ถูกใจ {{ "(" . favoritesCount() . ")" }}
                    </span>
                </div>
                <!-- Mobile -->
                <div class="d-md-none">
                    <img src="{{ asset('icons/oval-interface/heart.svg') }}" alt="Heart Icon" width="24">
                    <div class="position-relative d-inline-block">
                        <span id="favorites-count-mobile" class="position-absolute translate-middle">
                            {{ favoritesCount() }}
                        </span>
                    </div>
                </div>
            </a>
        @endif
@endsection

@section('content')
@yield('content')
@endsection

