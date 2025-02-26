@extends('layouts.shop')

@section('content')

    @if(session('cart'))
        <div class="row g-5 my-0">
            <div class="col-md-5 col-lg-4 order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">ตะกร้าสินค้า</span>
                    <span class="badge bg-primary rounded-pill">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                </h4>
                <ul class="list-group mb-3">
                    @foreach(session('cart') as $id => $item)
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">{{ $item['name'] }}</h6>
                                <small class="text-body-secondary">{{ Str::limit($item['description'], 8) }}</small>
                            </div>
                            <span class="text-body-secondary">
                                ฿{{ $item['price'] }}
                            </span>
                        </li>
                    @endforeach
                </ul>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">ล้างตะกร้า</button>
                </form>
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

@endsection
