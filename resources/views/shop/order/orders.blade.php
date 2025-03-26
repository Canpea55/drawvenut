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
            @foreach ($orders as $order)
                <div class="col-md-4">
                    <div class="animU row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                        <div class="col p-4 d-flex flex-column position-static">

                            <!-- Order ID, Total, and Date -->
                            <a class="no-underline" href="{{route('order', [$order->id])}}">
                                <strong class="d-inline-block mb-2 text-primary-emphasis">รหัสคำสั่งซื้อที่ {{$order->id}}</strong>
                                <div class="d-flex justify-content-between">
                                    <div class="mb-1 text-body-secondary">{{ convertTimezone($order->created_at)  }}</div>
                                    <div class="mb-1 text-body-secondary">{{ calculateDuration($order->created_at, now()) }}</div>
                                </div>
                                @switch($order->status)
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
                                    @case("canceling")
                                        <div>
                                            <h1 class="text-warning" style="margin-top: 0;">กำลังตรวจสอบเพื่อคืนเงินและยกเลิก...</h1>
                                        </div>
                                        @break
                                    @case("cancelled")
                                        <div>
                                            <h1 class="text-danger" style="margin-top: 0;">ยกเลิกคำสั่งซื้อแล้ว</h1>
                                        </div>
                                        @break
                                    @case("refund")
                                        <div>
                                            <h1 class="text-success" style="margin-top: 0;">คืนเงินสำเร็จ</h1>
                                        </div>
                                        @break
                                    @case("not-refund")
                                        <div>
                                            <h1 class="text-danger" style="margin-top: 0;">ยกเลิกคำสั่งซื้อและไม่ขอคืนเงิน</h1>
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
                                                if ($orderitem->order_id == $order->id) {
                                                    $count++;
                                                }
                                            }
                                        @endphp
                                        {{ $count }}
                                    </span>
                                </h4>
                                <ul class="list-group">
                                    @php
                                        // Group order items for the current order by product_id for easier processing
                                        $itemsGrouped = collect($orderItems)
                                            ->where('order_id', $order->id)
                                            ->groupBy('product_id');
                                    @endphp

                                    @foreach ($itemsGrouped as $productId => $items)
                                        <div class="list-group-item list-group-item-primary d-flex align-items-center lh-sm">
                                            <div class="w-100">
                                                <a href="{{ route('product', $productId) }}" class="d-flex justify-content-between align-items-center w-100 no-underline">
                                                    <div class="d-flex align-items-center p-2">
                                                        <div>
                                                            <h6 class="my-1">{{ Str::limit($items->first()->product_name, 32) }}</h6>
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-primary rounded-pill">{{ $items->count() }}</span>
                                                </a>
                                                <div class="my-2 w-100">
                                                    <ul class="list-group">
                                                        @foreach ($items as $option)
                                                            <a href="{{ route('product', ['id' => $option->product_id, 'option_id' => $option->product_options_id]) }}" class="animU list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                                <div class="d-flex justify-content-between align-items-center gap-3">
                                                                    @if ($option->option_thumbnail != null)
                                                                        <img class="rounded me-1 img-thumbnail" width="64" height="auto" src="{{ asset($option->option_thumbnail) }}" >
                                                                    @else
                                                                        <svg class="bd-placeholder-img card-img-top rounded" width="64" height="64" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false">
                                                                            <title>no image</title>
                                                                            <rect width="100%" height="100%" fill="#D09CC7"></rect>
                                                                        </svg>
                                                                    @endif
                                                                    <div>
                                                                        <div>{{ $option->option_name }}</div>
                                                                        <div class="text-success">฿{{ $option->option_price }}</div>
                                                                    </div>
                                                                </div>
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
                                            <strong class="text-success">฿{{$order->total}}</strong>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-auto d-none d-lg-block">
                            <a class="underline-none" href="{{route('order', [$order->id])}}">
                                @if ($order->receipt)
                                    {{-- with image --}}
                                    {{-- <img width="200" height="100%" src="{{ Storage::url($item->receipt) }}" alt="Product Thumbnail"
                                        class="card-img-top" style="width: 200px; height: 100%; object-fit: contain;"> --}}
                                @else
                                    {{-- no image --}}
                                    {{-- <svg class="bd-placeholder-img" width="200" height="" xmlns="http://www.w3.org/2000/svg"
                                        role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice"
                                        focusable="false">
                                        <rect width="100%" height="100%" fill="#d09cc7" />
                                    </svg> --}}
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