@extends('layouts.shop')

@section('style')
    .no-underline {
    text-decoration: none;
    }
@endsection

@section('content')
    <div class="container">
        @if(session('cart'))
            <div class="row g-3 my-0">
                <div class="col-md-5 col-lg-4 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary">ตะกร้าสินค้า</span>
                        <span class="badge bg-primary rounded-pill">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                    </h4>
                    <ul class="list-group my-3">
                        <li class="list-group-item">
                            <div class="d-flex flex-row-reverse">
                                <form action="{{ route('cart.clear') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">ล้างตะกร้า</button>
                                </form>
                            </div>
                        </li>
                        @php
                            $total = 0;
                        @endphp
                        @foreach(session('cart') as $id => $item)
                            <li class="list-group-item d-flex align-items-center justify-content-between lh-sm">
                                <div class="d-flex align-items-center gap-3">

                                    <!-- Image Section -->
                                    <a class="no-underline" href="{{ route('product', $id) }}">
                                        @if(array_key_exists('image', $item) && $item['image'])
                                            <img src="{{ Storage::url($item['image']) }}" alt="Product Thumbnail"
                                                class="rounded flex-shrink-0 card"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <svg class="bd-placeholder-img rounded flex-shrink-0 card" width="50" height="50"
                                                xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail"
                                                preserveAspectRatio="xMidYMid slice" focusable="false">
                                                <rect width="100%" height="100%" fill="#D09CC7" />
                                            </svg>
                                        @endif
                                    </a>

                                    <!-- Text Section (Takes up remaining space) -->
                                    <div class="flex-grow-1">
                                        <a class="no-underline" href="{{ route('product', $id) }}">
                                            <h6 class="my-1">{{ Str::limit($item['name'], 24) }}</h6>
                                        </a>
                                        <span class="text-body-secondary">฿{{ $item['price'] }}</span>
                                    </div>
                                    @php
                                        $total += $item['price'];
                                    @endphp

                                </div>

                                <!-- Remove Button -->
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <button type="submit" class="btn btn-danger">X</button>
                                </form>
                            </li>
                        @endforeach
                        <li class="list-group-item">
                            <div class="my-3 d-flex justify-content-between">
                                <span>รวม (THB)</span>
                                <strong>฿{{$total}}</strong>
                            </div>
                        </li>
                    </ul>

                    <div class="my-3">
                        <form action="{{ route('order.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="products" value="{{json_encode(session('cart'))}}">
                            <input type="hidden" name="total" value="{{ $total }}">
                            <button type="submit" class="w-100 btn btn-success btn-lg">ซื้อสินค้า</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-5 my-0">
                <div class="col-md-5 col-lg-4 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary">ตะกร้าสินค้า</span>
                        <span class="badge bg-primary rounded-pill">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                    </h4>
                </div>
            </div>
        @endif
    </div>
@endsection