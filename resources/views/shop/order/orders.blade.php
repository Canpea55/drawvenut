@extends('layouts.shop')

@section('title')
    คำสั่งซื้อของฉัน
@endsection

@section('style')
    .no-underline {
    text-decoration: none;
    }
@endsection

@section('content')
    @if (count($orders) > 0)
        <div class="py-4">
            <h1 class="text-info-emphasis">คำสั่งซื้อของฉัน</h1>
        </div>

        <div class="row row-cols-1 row-cols-md-2 g-3" data-masonry='{"percentPosition": true }'>
            @foreach ($orders as $item)
                <div class="col-md-6">
                    <div class="anim row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                        <div class="col p-4 d-flex flex-column position-static">

                            <!-- Order ID, Total, and Date -->
                            <a class="no-underline" href="{{route('order', [$item->id])}}">
                                <strong class="d-inline-block mb-2 text-primary-emphasis">รหัสคำสั่งซื้อที่ {{$item->id}}</strong>
                                <div class="mb-1 text-body-secondary">{{ convertTimezone($item->created_at)  }}</div>
                                @switch($item->status)
                                    @case("pending")
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h1 class="text-primary" style="margin-top: 0;">รอส่งสลิป</h1>
                                            </div>
                                        </div>
                                        @break
                                    @case("submitted")
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h1 class="text-info" style="margin-top: 0;">รอตรวจสอบบิล</h1>
                                            </div>
                                        </div>
                                        @break
                                    @case("success")
                                        <div class="d-flex justify-content-between">
                                            <h1 class="text-success" style="margin-top: 0;">ส่งสินค้าเรียบร้อย</h1>
                                        </div>
                                        @break
                                    @default
                                        <div>
                                            <h1 class="text-danger" style="margin-top: 0;">มีปัญหาที่ระบบ โปรดแจ้งเจ้าหน้าที่</h1>
                                        </div>
                                        @break
                                @endswitch
                            </a>
                            <p class="card-text mb-auto"></p>

                            <!-- Order Items -->
                            <div class="">
                                <h4 class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="text-primary-emphasis">สินค้าที่สั่ง</span>
                                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                        @php
                                            $count = 0;
                                            foreach ($orderItems as $id => $orderitem) {
                                                if ($orderitem->order_id == $item->id) {
                                                    $count++;
                                                }
                                            }
                                        @endphp
                                        {{ $count }}
                                    </span>
                                </h4>
                                <ul class="list-group">
                                    @foreach($orderItems as $id => $orderitem)
                                        @if ($orderitem->order_id == $item->id)
                                            <li class="list-group-item d-flex align-items-center justify-content-between lh-sm">
                                                <div class="d-flex align-items-center gap-3">

                                                    <!-- Image Section -->
                                                    <a class="no-underline" href="{{ route('product', $orderitem->product_id) }}">
                                                        @if(!empty($orderitem->thumbnail))
                                                            <img src="{{ Storage::url($orderitem->thumbnail) }}" alt="Product Thumbnail"
                                                                class="rounded flex-shrink-0 card"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <svg class="bd-placeholder-img rounded flex-shrink-0 card" width="50" height="50"
                                                                xmlns="http://www.w3.org/2000/svg" role="img"
                                                                aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice"
                                                                focusable="false">
                                                                <rect width="100%" height="100%" fill="#D09CC7" />
                                                            </svg>
                                                        @endif
                                                    </a>

                                                    <!-- Text Section (Takes up remaining space) -->
                                                    <div class="flex-grow-1">
                                                        <a class="no-underline" href="{{ route('product', $orderitem->product_id) }}">
                                                            <h6 class="my-1">{{ Str::limit($orderitem->name, 24) }}</h6>
                                                        </a>
                                                        <span class="text-body-secondary">฿{{ $orderitem->price }}</span>
                                                    </div>

                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                    <li class="list-group-item">
                                        <div class="my-3 d-flex justify-content-between">
                                            <span>รวม (THB)</span>
                                            <strong>฿{{$item->total}}</strong>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <!-- Order Duration -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-body-secondary">{{ calculateDuration($item->updated_at, now()) }}ที่แล้ว</small>
                            </div>
                        </div>

                        <div class="col-auto d-none d-lg-block">
                            <a class="underline-none" href="{{route('order', [$item->id])}}">
                                @if ($item->receipt)
                                    {{-- with image --}}
                                    <img width="200" height="100%" src="{{ Storage::url($item->receipt) }}" alt="Product Thumbnail"
                                        class="card-img-top" style="width: 200px; height: 100%; object-fit: cover;">
                                @else
                                    {{-- no image --}}
                                    <svg class="bd-placeholder-img" width="200" height="" xmlns="http://www.w3.org/2000/svg"
                                        role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice"
                                        focusable="false">
                                        <rect width="100%" height="100%" fill="#d09cc7" />
                                    </svg>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{-- {{ $orders->links() }} --}}
    @else
        <div class="text text-center">ยังไม่มีคำสั่งซื้อใดๆ</div>
    @endif
@endsection