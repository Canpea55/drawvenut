@extends('layouts.shop')

@section('style')

    .bd-placeholder-img {
    font-size: 1.125rem;
    text-anchor: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    }

    @media (min-width: 768px) {
    .bd-placeholder-img-lg {
    font-size: 3.5rem;
    }
    }

    .b-example-divider {
    width: 100%;
    height: 3rem;
    background-color: rgba(0, 0, 0, .1);
    border: solid rgba(0, 0, 0, .15);
    border-width: 1px 0;
    box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
    }

    .b-example-vr {
    flex-shrink: 0;
    width: 1.5rem;
    height: 100vh;
    }

    .bi {
    vertical-align: -.125em;
    fill: currentColor;
    }

    .nav-scroller {
    position: relative;
    z-index: 2;
    height: 2.75rem;
    overflow-y: hidden;
    }

    .nav-scroller .nav {
    display: flex;
    flex-wrap: nowrap;
    padding-bottom: 1rem;
    margin-top: -1px;
    overflow-x: auto;
    text-align: center;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    }

    .btn-bd-primary {
    --bd-violet-bg: #712cf9;
    --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

    --bs-btn-font-weight: 600;
    --bs-btn-color: var(--bs-white);
    --bs-btn-bg: var(--bd-violet-bg);
    --bs-btn-border-color: var(--bd-violet-bg);
    --bs-btn-hover-color: var(--bs-white);
    --bs-btn-hover-bg: #6528e0;
    --bs-btn-hover-border-color: #6528e0;
    --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
    --bs-btn-active-color: var(--bs-btn-hover-color);
    --bs-btn-active-bg: #5a23c8;
    --bs-btn-active-border-color: #5a23c8;
    }

    .bd-mode-toggle {
    z-index: 1500;
    }

    .bd-mode-toggle .dropdown-menu .active .bi {
    display: block !important;
    }

    .bg-banner {
        position: relative;
        background: url('{{ asset('images/fb-banner.png') }}') center/cover no-repeat;
        height: 400px;
        color: white;
    }

    .bg-banner::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        {{-- background: rgba(255, 255, 255, 0.60); --}}
        z-index: 1;
    }

    .bg-banner .content {
        position: relative;
        z-index: 2; /* Ensures text appears above overlay */
    }

    .no-underline {
        text-decoration: none;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.5s ease;
        opacity: 0.9;
        will-change: transform, opacity, box-shadow;
    }
    .card:hover {
        transform: translateY(-5px); /* Moves card up slightly */
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2); /* Adds shadow */
        opacity: 1;
    }

@endsection

@section('title')
    ร้านค้า
@endsection

@section('content')

    <div class="rounded position-relative overflow-hidden m-md-3 text-center bg-banner">
        <div class="row h-100 d-flex align-items-center justify-content-center content">
            {{-- <div class="col-lg-6 col-md-8 mx-auto">
                <h1 class="fw-light text-primary-emphasis">ห้องสื่อสวย</h1>
                <p class="fw-light text-body-secondary">ไม่เน้นขาย เน้นแจก</p>
            </div> --}}
        </div>
    </div>

    @if (count($products) > 0)
        
        <div class="album py-4 bg-body-tertiary">
            <h2 class="mx-3 py-1">สินค้าใหม่</h2>
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" data-masonry='{"percentPosition": true }'>
                    @foreach ($products as $item)
                        <div class="col">
                            <div class="card shadow-sm">
                                @auth
                                    @if (Auth::user()->isAdmin)
                                        <div class="d-flex justify-content-between align-items-start" style="position: absolute; right: 3%; top: 4%;">
                                            <div class="btn-group gap-1">
                                                <a href="{{route('product.edit', $item->id)}}" type="button" class="opBtn" title="แก้ไข">
                                                    <img src="{{ asset('icons/oval-interface/edit.svg') }}" alt="Heart Icon" width="24">
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endauth
                                <a class="no-underline" href="{{route('product', $item->id) }}" >
                                    @php
                                        $images = json_decode($item->images, true);
                                        $image = isset($images[0]) ? $images[0] : null; // Get the first image, or null if not available
                                    @endphp
                                    @if($image)
                                        <img src="{{ Storage::url($image) }}" alt="{{$item->name}}" class="card-img-top"
                                            style="width: 100%; min-height: 225px; max-height: 400px; object-fit: contain;">
                                    @else
                                        <svg class="bd-placeholder-img card-img-top" width="100%" height="225"
                                            xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail"
                                            preserveAspectRatio="xMidYMid slice" focusable="false">
                                            <title>no image</title>
                                            <rect width="100%" height="100%" fill="#D09CC7" />
                                        </svg>
                                    @endif
                                    <div class="card-body" >
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="d-flex text-primary-emphasis align-items-center" style="width: 85%">{{ $item->name }}</strong>
                                            <strong class="text-success flex-shrink-0 m-0">{{ $item->price }} ฿</strong>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{ $products->links() }}
    @else
        <div class="text text-center">ไม่มีสินค้าจะขายแล้วจ้า</div>
    @endif

@endsection