@extends('layouts.shop')

@section('style')
    .no-underline {
        text-decoration: none;
    }
    .product-menu {
        overflow-y: scroll;
    }
    /* hover animation */
    .anim {
        transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.5s ease, background-color 0.3s ease, color 0.3s ease;
        opacity: 0.8;
    }
    .anim:hover {
        transform: translateY(-2px);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
        background-color: #D09CC7; 
        color: white;
        opacity: 1;
    }
    .selected {
        
    }    
@endsection

@section('head-script')
    <script>
        function setIframe(x) {
            const oldSelected = document.getElementsByClassName("selected");
            try {
                for (let i = 0; i < oldSelected.length; i++) {
                    oldSelected[i].style.backgroundColor = '';
                    oldSelected[i].style.color = '';
                    oldSelected[i].classList.remove("selected");
                }
            } catch(err) {}
            const selected = document.getElementById(x.toString());
            selected.style.backgroundColor = '#D09CC7';
            selected.style.color = '#fff';
            selected.classList.add("selected");
            const contentframe = document.getElementById("contentframe");
            contentframe.src = '/library/product/' + x;
        }

        $(document).ready(function(){
            $('#search-input').on('input', function(){
                const query = $(this).val().toLowerCase().trim();
                // console.log('Search query:', query);

                // Select all product items and filter them
                $('.product-menu .list-group-item').each(function(){
                    const $item = $(this);
                    const productName = $item.find('.flex-grow-1 h6').text().toLowerCase();
                    // console.log('Product:', productName, 'Contains query:', productName.includes(query));
                    
                    if (query === '' || productName.includes(query)) {
                        $item.css('display', '');  // Show the item
                    } else {
                        $item.css('display', 'none');  // Hide the item
                    }
                });
            });
        });
    </script>
    
    <script>
        var $countEl;
        var $countMobileEl;
        var $favoritesLink;
        var $currentCount;
        var $newCount;

        $(document).ready(function(){
            $countEl = $('#favorites-count');
            $countMobileEl = $('#favorites-count-mobile');
            $favoritesLink = $('#favorites-link');
            $currentCount = $countEl.length ? parseInt($countEl.text().replace(/[^\d]/g, '')) || 0 : 0;
            $newCount = $currentCount;
            $('.favorite-form').on('submit', function(e){
                e.preventDefault();
                var form = $(this);
                var btnImage = form.find('img');
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    success: function(response){
                        if(response.favorite === 'removed'){
                            updateFavoritesCount(-1);
                            btnImage.attr('src', '{{ asset("icons/oval-interface/heart.svg") }}');
                        } else if(response.favorite === 'added'){
                            updateFavoritesCount(1);
                            btnImage.attr('src', '{{ asset("icons/oval-interface/heart-filled.svg") }}');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.error("Error in favorite toggle:", errorThrown);
                    }
                });
            });
            updateFavoritesCount(0);
        });

        // Function to update the favorites count and toggle the favorites button visibility
        function updateFavoritesCount(delta) {            
            $newCount += delta;
            if ($countEl.length || $countMobileEl.length) {                
                if($newCount > 0){
                    // Update the count for desktop view if it exists
                    if ($countEl.length) {
                        $countEl.text('สิ่งที่ถูกใจ (' + $newCount + ')');
                    }
                    // Update the mobile view count if exists
                    if($countMobileEl.length) {
                        $countMobileEl.text($newCount);
                    }
                    // Make sure the favorites button is visible if it exists
                    if ($favoritesLink.length) {
                        $favoritesLink.fadeIn(300);
                    }
                } else {
                    // If count is 0 or lower, hide the favorites button if it exists
                    if ($favoritesLink.length) {
                        $favoritesLink.fadeOut(300);
                    }
                }
            }
        }
    </script>
@endsection

@section('title')
    คลัง
@endsection

@section('content')
    @if(count($products) > 0)
        <!-- This div is visible on large screens (PC) but hidden on small screens (phones) -->
        <div class="d-none d-md-block">
            <div class="row g-4">
                <div class="col-md-5 col-lg-4 ">
                    <ul class="list-group product-menu" style="height: 80%">
                        <!-- Search bar for filtering product list -->
                        <input type="text" id="search-input" class="form-control mb-3" placeholder="ค้นหาของในคลัง...">
                        @foreach($products as $item)
                            <a id="{{$item->id}}" class="list-group-item anim no-underline stretched-link anim" href="#" onclick="setIframe({{$item->id}});return false;">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Image Section -->
                                    @php
                                        $defaultImage = null;
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
                                    <div class="flex-grow-1">
                                        <h6 class="my-1">{{ Str::limit($item['name'], 24) }}</h6>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </ul>
                </div>

                <div class="col-md-7 col-lg-8">
                    <iframe id="contentframe" src="{{route('library.product.info', $products[0]->id)}}" width="100%"
                            height="1000vh" style="border: none;"></iframe>
                </div>
            </div>
        </div>

        <!-- This div is visible on small screens (phones) but hidden on larger screens -->
        <div class="d-md-none">
            <div class="album py-4 bg-body-tertiary">
                <div class="container">
                    <div class="grid row row-cols-1 row-cols-sm-2 g-2">
                        @foreach ($products as $item)
                            <div class="col grid-item">
                                <div class="card shadow-sm">
                                    @auth
                                        <div class="d-flex justify-content-between align-items-start" style="position: absolute; right: 3%; top: 4%; z-index: 1;">
                                            <div class="btn-group gap-1">
                                                @if (Auth::user()->isAdmin)
                                                    <a href="{{route('product.edit', $item->id)}}" type="button" class="opBtn" title="แก้ไข">
                                                        <img src="{{ asset('icons/oval-interface/edit.svg') }}" alt="edit" width="24">
                                                    </a>
                                                @endif
                                                <form method="POST" action="{{ route('favorite') }}" class="favorite-form">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item->id }}">
                                                    <button type="submit" class="opBtn p-0 border-0 bg-transparent" title="ถูกใจ">
                                                        @if (isFavorited($item->id))
                                                            <img src="{{ asset('icons/oval-interface/heart-filled.svg') }}" alt="favorite" width="24">
                                                        @else
                                                            <img src="{{ asset('icons/oval-interface/heart.svg') }}" alt="favorite" width="24">
                                                        @endif
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endauth
                                    <a class="no-underline" href="{{route('product', $item->id) }}">
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
                                        <div class="card-body">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <strong class="d-flex text-primary-emphasis align-items-center"
                                                    style="width: 85%">{{ $item->name }}</strong>
                                            </div>
                                            <div id="tags-container" class="d-flex flex-wrap mt-2">
                                                @if($item->tags && $item->tags->count() > 0)
                                                    @foreach($item->tags as $tag)
                                                        <h6 class="me-1">
                                                            <a href="{{ route('search', ['keyword' => $tag->name]) }}" class="badge btn btn-primary btn-sm">
                                                                {{ $tag->name }}
                                                            </a>
                                                        </h6>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1 class="display-1">📦</h1>
                    <h2>ไม่มีสินค้าในคลัง</h2>
                    <p class="lead">มาเริ่มซื้อสินค้าชิ้นแรกของคุณกันเลยค่ะ!</p>
                    <a href="{{route('index')}}" class="btn btn-lg btn-primary">ไปที่หน้าร้านค้า</a>
                </div>
            </div>
        </div>
    @endif
@endsection