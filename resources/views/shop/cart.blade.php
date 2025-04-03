@extends('layouts.shop')

@section('style')
    .no-underline {
    text-decoration: none;
    }
@endsection

@section('title')
ตะกร้าสินค้า
@endsection

@section('content')
    <div class="container">
        @if(session('cart'))
            @php
                // Get count of all option in cart
                $optionCount = 0;
                $cart_items = session()->get('cart', []);
                foreach($cart_items as $product){
                    $optionCount += count($product['selectedOptions']);
                }
            @endphp
            <div class="row g-3 my-0">
                <div class="col-md-6 col-lg-4 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-dark">ตะกร้าสินค้า</span>
                        <span class="badge bg-dark rounded-pill">{{ $optionCount }}</span>
                    </h4>
                    <div class="list-group my-3">
                        <div class="list-group-item list-group-item-primary">
                            <div class="d-flex flex-row-reverse">
                                <form action="{{ route('cart.clear') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">ล้างตะกร้า</button>
                                </form>
                            </div>
                        </div>
                        @php
                            $total = 0;
                        @endphp
                        @foreach($cart_items as $product_id => $item)
                            @php
                                $product_total = 0;
                                foreach($item['optionDetails'] as $option) {
                                    $product_total += $option['price'];
                                }

                                $product_optionCount = count($item['selectedOptions']);
                            @endphp
                            <div class="list-group-item list-group-item-primary d-flex align-items-center lh-sm">
                                <div class="w-100">
                                    <a href="{{ route('product', $product_id) }}" class="d-flex justify-content-between align-items-center w-100 no-underline">
                                        <div class="d-flex align-items-center p-2">
                                            {{-- <div class="">
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
                                            </div> --}}
                                            <div>
                                                <h6 class="my-1">{{ Str::limit($item['name'], 24) }}</h6>
                                            </div>
                                            @php
                                                $total += $product_total;
                                            @endphp
                                        </div>
                                        <span class="badge bg-primary rounded-pill">{{$product_optionCount}}</span>
                                    </a>
                                    <div class="my-2 w-100">
                                        <ul class="list-group">
                                            @foreach($item['selectedOptions'] as $index => $option)
                                            <a href="{{ route('product', ['id' => $product_id, 'option_id' => $option]) }}" class="animU list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                <div class="d-flex justify-content-between align-items-center gap-3">
                                                    @if ($item['optionDetails'][$index]['thumbnail'] != null)
                                                        <img class="rounded me-1 img-thumbnail" width="128" height="auto" src="{{ asset($item['optionDetails'][$index]['thumbnail']) }}" >
                                                    @else
                                                        <svg class="bd-placeholder-img card-img-top rounded" width="128" height="128" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false">
                                                            <title>no image</title>
                                                            <rect width="100%" height="100%" fill="#D09CC7"></rect>
                                                        </svg>
                                                    @endif
                                                    <div>
                                                        <div class="">{{ $item['optionDetails'][$index]['name'] }}</div>
                                                        <div class="text-success">฿{{ $item['optionDetails'][$index]['price'] }}</div>
                                                    </div>
                                                </div>
                                                <form action="{{ route('cart.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{$product_id}}">
                                                    <input type="hidden" name="option_id" value="{{$option}}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">ลบ</button>
                                                </form>
                                            </a>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <li class="list-group-item list-group-item-primary">
                            <div class="my-1 p-2 d-flex justify-content-between">
                                <span>รวม (THB)</span>
                                <strong class="text-success">฿{{$total}}</strong>
                            </div>
                        </li>
                    </div>

                    <div class="my-3">
                        <form action="{{ route('order.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="items" value="{{json_encode(session('cart'))}}">
                            <input type="hidden" name="total" value="{{ $total }}">
                            @if ($total <= 0)
                                <button type="submit" class="w-100 btn btn-success btn-lg">รับสินค้าฟรี</button>
                            @else
                                <button type="submit" class="w-100 btn btn-primary btn-lg">ซื้อสินค้า</button>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="col-md-6 col-lg-8">
                    <div class="d-flex flex-glow card h-100 justify-content-center align-items-center">
                        <figure class="text-center">
                            <blockquote class="blockquote">
                              <p>แสดงสินค้าที่เกี่ยวข้องโดยอ้างอิงจาก tag ในตะกร้า</p>
                            </blockquote>
                            <figcaption>
                               <p><small>placeholder</small></p>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection