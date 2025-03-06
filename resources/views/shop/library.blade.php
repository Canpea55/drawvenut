@extends('layouts.shop')

@section('style')
    .no-underline {
    text-decoration: none;
    }
@endsection

@section('title')
    คลัง
@endsection

@section('content')
    <!-- This div is visible on large screens (PC) but hidden on small screens (phones) -->
    <div class="d-none d-md-block">
        <div class="row g-4 my-0">
            <div class="col-md-5 col-lg-4">
                <ul class="list-group">
                    @foreach($products as $item)
                                    <li class="list-group-item d-flex align-items-center justify-content-between lh-sm anim">
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Image Section -->
                                            <a class="no-underline stretched-link" href="{{ route('product', $item->id) }}">
                                                @php
                                                    $images = json_decode($item->images, true);
                                                    if (is_array($images) && count($images) > 0) {
                                                        $defaultImage = $images[0];
                                                    }
                                                @endphp
                                                @if($defaultImage)
                                                    <img src="{{Storage::url($defaultImage)}}" alt="thumbnail"
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
                                            <div class="flex-grow-1">
                                                <h6 class="my-1">{{ Str::limit($item['name'], 24) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                    @endforeach

                </ul>
            </div>

            <div class="col-md-7 col-lg-8 card">
                <div class="">
                    {{-- product data like in product page from shop --}}
                    <iframe src="{{route('product', 24)}}" width="100%" height="1000vh" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- This div is visible on small screens (phones) but hidden on larger screens -->
    <div class="d-md-none">
        fdsaag
    </div>
@endsection