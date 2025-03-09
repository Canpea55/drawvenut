@extends('layouts.app')

@section('title', 'จัดการสินค้า')

@section('subnavbar-left')
    <div class="d-flex" style="height: 100%;">
        <div class="vr"></div>
    </div>
    <a class="opBtn no-underline px-2 py-1" href="{{route("product.add")}}" title="เพิ่มสินค้า">
        <img src="{{ asset('icons/oval-interface/circle-add.svg') }}" alt="Add Icon" width="24">
        {{-- <span for="addProduct" class="fw-light">เพิ่มสินค้า</span> --}}
    </a>
@endsection

@section('content')
    <div class="py-4">
        <h1 class="">จัดการสินค้าทั้งหมด</h1>
    </div>

    @if (count($products) > 0)
        <div class="album bg-body-tertiary">
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-3 row-cols-md-4 g-3" data-masonry='{"percentPosition": true }'>
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
                                </a>
                                <div class="card-body" >
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="d-flex text-primary-emphasis align-items-center" style="width: 85%">{{ $item->name }}</strong>
                                        <strong class="text-success flex-shrink-0 m-0">{{ $item->price }} ฿</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="d-flex text-primary-emphasis align-items-center" style="width: 85%">เพิ่มเมื่อ</small>
                                        <small class="text-primary-emphasis flex-shrink-0 m-0">{{ convertTimezone($item->created_at) }}</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="d-flex text-primary-emphasis align-items-center" style="width: 85%">แก้ไขล่าสุดเมื่อ</small>
                                        <small class="text-primary-emphasis flex-shrink-0 m-0">{{ convertTimezone($item->updated_at) }}</small>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <form action="{{ route('product.toggle.downloadable', $item->id) }}" method="POST">
                                            @csrf
                                            <label class="switch">
                                                <input type="checkbox" id="isDownloadable-{{ $item->id }}" name="isDownloadable" class="form-check-input" {{ $item->isDownloadable ? 'checked' : '' }} onchange="this.form.submit()">
                                                <span class="slider"></span>
                                            </label>
                                            <span for="isDownloadable-{{ $item->id }}" class="form-check-label align-top">ดาวน์โหลดได้?</span>
                                        </form>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <form action="{{ route('product.toggle.buyable', $item->id) }}" method="POST">
                                            @csrf
                                            <label class="switch">
                                                <input type="checkbox" id="isBuyable-{{ $item->id }}" name="isBuyable" class="form-check-input" {{ $item->isBuyable ? 'checked' : '' }} onchange="this.form.submit()">
                                                <span class="slider"></span>
                                            </label>
                                            <span for="isBuyable-{{ $item->id }}" class="form-check-label align-top">ซื้อได้?</span>
                                        </form>
                                    </div>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <form action="{{ route('product.toggle.publish', $item->id) }}" method="POST">
                                            @csrf
                                            <label class="switch">
                                                <input type="checkbox" id="isPublish-{{ $item->id }}" name="isPublish" class="form-check-input" {{ $item->isPublish ? 'checked' : '' }} onchange="this.form.submit()">
                                                <span class="slider"></span>
                                            </label>
                                            <span for="isPublish-{{ $item->id }}" class="form-check-label align-top">เผยแพร่?</span>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="pt-4">
            {{ $products->links() }}
        </div>
    @else
        <div class="text text-center">ไม่มีสินค้าในระบบ</div>
    @endif
@endsection
