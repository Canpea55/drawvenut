@extends('layouts.shop')

@section('title')
    {{ $product->name }}
@endsection

@section('style')
    .thumbnail-container {
    display: flex;
    overflow-x: auto;
    white-space: nowrap;
    gap: 8px;
    padding-bottom: 8px;
    }

    .thumbnail-container::-webkit-scrollbar {
    height: 8px;
    }
    .thumbnail-container::-webkit-scrollbar-track {
    background: transparent;
    }
    .thumbnail-container::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 4px;
    }

    /* Default thumbnail size for larger screens */
    .thumbnail-img {
    width: 10%;
    height: auto;
    object-fit: contain;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.3s ease-in-out;
    }
    .thumbnail-img.active {
    border-color: #007bff;
    }

    /* Increase thumbnail size for mobile devices */
    @media (max-width: 767.98px) {
        .thumbnail-img {
        width: 15%;
        }
    }

    picture {
        height: 400px;
        width: 100%;
        aspect-ratio: 4 / 3; /* Adjust aspect ratio as needed */
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    picture img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

@endsection

@section('foot-script')
    {{-- main --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const thumbnails = document.querySelectorAll(".thumbnail-img");
            const mainImage = document.getElementById("mainImage");
            
            function updateMainImage(newSrc, clickedElement, elementsToUpdate) {
                if (mainImage) {
                    mainImage.src = newSrc;
                }
                elementsToUpdate.forEach(el => el.classList.remove("active"));
                clickedElement.classList.add("active");
            }
            
            // Thumbnail click handler
            thumbnails.forEach(function (thumbnail) {
                thumbnail.addEventListener("click", function () {
                    updateMainImage(
                        this.getAttribute("data-image"),
                        this,
                        thumbnails
                    );
                });
            });
            
            const optionBadges = document.querySelectorAll(".option-badge");
            const priceDisplay = document.getElementById('price-display');
            const selectedOptionInput = document.getElementById("selected_option");

            if(optionBadges.length == 0){
                document.querySelector('.cart-form button[type="submit"]').disabled = false;
            }
            
            optionBadges.forEach(function(badge) {
                badge.addEventListener("click", function(e) {
                    e.preventDefault();
            
                    const newSrc = this.getAttribute("data-image");
            
                    // Toggle indicator:
                    // If this badge is already selected, remove the indicator and clear the selected_option value.
                    let currentIndicator = this.querySelector('.selected-indicator');
                    if(currentIndicator) {
                        currentIndicator.remove();
                        selectedOptionInput.value = "";
                    } else {
                        // Remove indicators from any badge that has it
                        document.querySelectorAll('.option-badge .selected-indicator').forEach(function(ind) {
                            ind.remove();
                        });
            
                        // Create and add the indicator badge
                        let indicator = document.createElement("span");
                        indicator.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success selected-indicator";
                        indicator.innerHTML = "✓<span class='visually-hidden'>unread messages</span>";
                        this.appendChild(indicator);
            
                        // Set the hidden field with the selected option id
                        selectedOptionInput.value = this.getAttribute("data-option");
                    }
            
                    // Update the main image
                    let found = false;
                    thumbnails.forEach(thumbnail => {
                        if (thumbnail.getAttribute("data-image") === newSrc) {
                            updateMainImage(newSrc, thumbnail, thumbnails);
                            found = true;
                        }
                    });
                    if (!found) {
                        mainImage.src = newSrc;
                    }

                    if(selectedOptionInput.value === '') {
                        priceDisplay.textContent = '฿' + "{{$product->prices}}";
                        document.querySelector('.cart-form button[type="submit"]').disabled = true;
                    } else {
                        // Update the price if available (adjust based on your badge structure)
                        let priceText = this.querySelector('div div:last-child').textContent;
                        if(priceText)
                        priceDisplay.textContent = priceText.trim();
                        document.querySelector('.cart-form button[type="submit"]').disabled = false;
                    }                    
                });
            });
            
            // Optionally, set the first thumbnail as active by default if exists
            if (thumbnails.length > 0) {
                thumbnails[0].classList.add("active");
            }
        });
    </script>

    {{-- button actions --}}
    <script>
        // $(document).ready(function(){
        //     $('.cart-form').on('submit', function(e){
        //         e.preventDefault();
        //         var form = $(this);
        //         $.ajax({
        //             url: form.attr('action'),
        //             method: form.attr('method'),
        //             data: form.serialize(),
        //             success: function(response){
        //                 // if(response.favorite === 'removed'){
        //                 //     updateFavoritesCount(-1);
        //                 //     btnImage.attr('src', '{{ asset("icons/oval-interface/heart.svg") }}');
        //                 //     bootstrap.Toast.getOrCreateInstance(document.getElementById('removeFavToast' + productID)).show();
        //                 // } else if(response.favorite === 'added'){
        //                 //     updateFavoritesCount(1);
        //                 //     btnImage.attr('src', '{{ asset("icons/oval-interface/heart-filled.svg") }}');
        //                 //     bootstrap.Toast.getOrCreateInstance(document.getElementById('addFavToast' + productID)).show();
        //                 // }
        //             },
        //             error: function(jqXHR, textStatus, errorThrown){
        //                 console.error("Error in favorite toggle:", errorThrown);
        //             }
        //         });
        //     });
    </script>

    {{-- favorite system --}}
    <script>
        var $countEl;
        var $countMobileEl;
        var $favoritesLink;
        var $currentCount;
        var $newCount;

        var productID = 0;
        function setProductID(x){
            productID = x;
        }

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
                            bootstrap.Toast.getOrCreateInstance(document.getElementById('addFavToast' + productID)).hide();
                            bootstrap.Toast.getOrCreateInstance(document.getElementById('removeFavToast' + productID)).show();
                        } else if(response.favorite === 'added'){
                            updateFavoritesCount(1);
                            btnImage.attr('src', '{{ asset("icons/oval-interface/heart-filled.svg") }}');
                            bootstrap.Toast.getOrCreateInstance(document.getElementById('addFavToast' + productID)).show();
                            bootstrap.Toast.getOrCreateInstance(document.getElementById('removeFavToast' + productID)).hide();
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

@section('content')
    <!-- Main Product Display -->
    <section class="flex card mb-2">
        <div class="d-flex justify-content-between align-items-start" style="position: absolute; right: 16px; top: 16px; z-index: 1;">
            <div class="btn-group gap-1">
                @auth
                    @if (Auth::user()->isAdmin)
                        <a href="{{route('product.edit', $product->id)}}" type="button" class="opBtn" title="แก้ไข">
                            <img src="{{ asset('icons/oval-interface/edit.svg') }}" alt="edit" width="24">
                        </a>
                    @endif

                    <form method="POST" action="{{ route('favorite') }}" class="favorite-form"  onsubmit="setProductID({{$product->id}});">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="opBtn p-0 border-0 bg-transparent" title="ถูกใจ">
                            @if (isFavorited($product->id))
                                <img src="{{ asset('icons/oval-interface/heart-filled.svg') }}" alt="favorite" width="24">
                            @else
                                <img src="{{ asset('icons/oval-interface/heart.svg') }}" alt="favorite" width="24">
                            @endif
                        </button>
                    </form>
                @endauth
                
            </div>
        </div>
        <div class="container p-4">
            <div class="row">
                <!-- Left Column: Main Image and Thumbnails -->
                <div class="col-12 col-lg-8 my-2">
                    @php
                        $images = json_decode($product->images, true);
                    @endphp
                    @if(is_array($images) && count($images) > 0)
                        @php
                            $defaultImage = $images[0];
                        @endphp
                        <!-- Main Image -->
                        <picture>
                            @if (!$defaultImage == null)
                                <img id="mainImage" src="{{ asset($defaultImage) }}" alt="Product Image" width="100%" class="rounded border">
                            @else
                                <svg class="bd-placeholder-img card-img-top" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false">
                                    <title>no image</title>
                                    <rect width="100%" height="100%" fill="#D09CC7"></rect>
                                </svg>
                                <p style="position: absolute;">ไม่มีภาพประกอบ</p>
                            @endif
                        </picture>
                        <!-- Horizontal Thumbnail Container -->
                        @if (!$defaultImage == null)
                            <div class="thumbnail-container mt-2">
                                @foreach($images as $img)
                                    <img src="{{ asset($img) }}" alt="Thumbnail" class="img-thumbnail thumbnail-img" data-image="{{ asset($img) }}">
                                @endforeach
                            </div>
                        @endif
                    @else
                        No Images Available
                    @endif
                </div>
                <!-- Right Column: Product Details -->
                <div class="col-12 col-lg-4 my-2 d-flex flex-column">
                    @php
                        if ($product->highestPrice <= 0) {
                            $pricetag = 'ฟรี!';
                        } else {
                            $pricetag = '฿' . $product->prices;
                        }
                    @endphp
                    <h2 id="name-display" class="fw-light">{{ $product->name }}</h2>
                    <h1 id="price-display" class="fw-semibold text-success">{{ $pricetag }}</h1>
                    <div id="tags-container" class="d-flex flex-wrap gap-1 my-2">
                        @if($product->tags && $product->tags->count() > 0)
                            @foreach($product->tags as $tag)
                                <h7>
                                    <a href="{{ route('search', ['keyword' => $tag->name]) }}" class="badge btn btn-primary btn-sm">
                                        {{ $tag->name }}
                                    </a>
                                </h7>
                            @endforeach
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-start overflow-y-scroll py-2" style="max-height: 40%">
                        @if(count($product->options) > 1)
                            @foreach($product->options as $option)
                                <a href="#" class="badge animU d-flex align-items-center p-1 pe-2 text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded no-underline option-badge" 
                                data-image="{{ asset($option->thumbnail) }}"
                                data-option="{{ $option->id }}">
                                    @if(isset($option->thumbnail))
                                        <img class="rounded me-1" width="auto" height="32" src="{{ asset($option->thumbnail) }}" alt="{{ $option->name }}">
                                    @endif
                                    <div>
                                        <div class="text-start mb-1">{{$option->name}}</div>
                                        <div class="text-start text-success">{{priceTag($option->price)}}</div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            
                        @endif
                    </div>
                    <div class="flex-grow-1"></div>
                    <div id="button-container" class="d-flex flex-wrap gap-2">
                        @if ($product->prices <= 0)
                            <form action="{{route('library.free')}}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{$product->id}}">
                                <button type="submit" class="btn btn-success btn-lg">เพิ่มไปยังคลัง</button>
                            </form>
                            {{-- <a type="button" class="btn btn-success btn-lg" href="{{route('library.free', ['product_id' => $product->id])}}">เพิ่มไปยังคลัง</a> --}}
                            @if ($product->isDownloadable && !empty($product->options[0]->downloads[0]))
                                <div class="btn-group dropdown-center">
                                    <a target="_top" href="{{$product->options[0]->downloads[0]->link}}" class="btn btn-lg btn-success" {{$product->options[0]->downloads[0]->isDownload ? 'download' : '' }}>{{$product->options[0]->downloads[0]->isDownload ? 'ดาวน์โหลด' : 'รับ' }} ({{$product->options[0]->downloads[0]->type}})</a>
                                    <button type="button" class="btn btn-lg btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu bg-primary-subtle" style="--bs-dropdown-min-width:12rem;">
                                        <p class="px-4 py-2 text-body-secondary text-center w-100 m-0">
                                            เนื้อหาที่สามารถดาวน์โหลดได้
                                        </p>
                                        <li><hr class="dropdown-divider"></li>
                                        @foreach ($product->options as $option)
                                            <div class="list-group-item list-group-item-primary d-flex align-items-center lh-sm">
                                                <div class="w-100">
                                                    <div class="d-flex justify-content-between align-items-center gap-3 w-100 no-underline px-2">
                                                        <img class="rounded me-1 img-thumbnail" width="64" height="auto" src="{{ asset($option->thumbnail) }}">
                                                        <h6 class="my-1">{{ Str::limit($option->name, 32) }}</h6>
                                                        <div class="d-flex flex-grow-1"></div>
                                                    </div>
                                                    <div class="my-2">
                                                        <ul class="list-group mx-2">
                                                            @foreach ($option->downloads as $download)
                                                                <a target="_top" href="{{$download->link}}" class="animU dropdown-item list-group-item list-group-item-action list-group-item-success d-flex justify-content-between align-items-center" {{$download->isDownload ? 'download' : '' }}>{{$download->isDownload ? 'ดาวน์โหลด' : 'รับ' }} ({{$download->type}})</a>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <a class="btn btn-success btn-lg disabled" role="button" aria-disabled="true" title="ไม่สามารถดาวห์โหลดได้">ดาวน์โหลด</a>
                            @endif
                        @else
                            <form action="{{ route('cart.add') }}" class="cart-form" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="product_name" value="{{ $product->name }}">
                                <input type="hidden" name="option_id" id="selected_option" value="{{count($product->options) > 1 ? '' : $product->options[0]->id}}">
                            
                                <button type="submit" class="btn btn-primary btn-lg" disabled>เพิ่มใส่ตะกร้า</button>
                            </form>
                        @endif
                        {{-- @if ($product->owned)
                            @if ($product->isDownloadable)
                                <div class="btn-group">
                                    <a class="btn btn-success btn-lg w-100" href="{{ $product->download }}" download>ดาวน์โหลด</a>
                                    <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">A</a></li>
                                        <li><a class="dropdown-item" href="#">B</a></li>
                                        <li><a class="dropdown-item" href="#">C</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#">D</a></li>
                                    </ul>
                                </div>
                            @else
                                <a class="btn btn-success btn-lg disabled w-100" role="button" aria-disabled="true" title="ขณะนี้ยังไม่สามารถดาวน์โหลดได้ค่ะ">ดาวน์โหลด</a>
                            @endif
                        @else
                            @if ($product->lowestPrice <= 0) 
                                @if ($product->isDownloadable)
                                    <a class="btn btn-success btn-lg " href="{{ $product->download }}" download>ดาวน์โหลด</a>
                                @else
                                    <a class="btn btn-success btn-lg disabled" role="button" aria-disabled="true" title="ขณะนี้ยังไม่สามารถดาวน์โหลดได้ค่ะ">ดาวน์โหลด</a>
                                @endif
                                <a type="button" class="btn btn-primary btn-lg disabled" aria-disabled="true" href="#">เพิ่มไปยังคลัง</a>
                            @else
                                @if (session('cart'))
                                    @if (array_key_exists($product->id, session('cart')))
                                        <a type="button" class="btn bg-success-subtle btn-lg" href="{{route('cart')}}">ไปที่รถเข็น</a>
                                    @else
                                        @if ($product->isBuyable)
                                            <form action="{{ route('cart.add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                @if(!empty($defaultImage))<input type="hidden" name="image" value="{{ $defaultImage }}">@endif
                                                <input type="hidden" name="name" value="{{ $product->name }}">
                                                <input type="hidden" name="description" value="{{ $product->description }}">
                                                <input type="hidden" name="price" value="{{ $product->price }}">
                                                <button type="submit" class="btn btn-primary btn-lg">เพิ่มใส่ตะกร้า</button>
                                            </form>
                                        @else
                                            <a class="btn btn-primary btn-lg disabled" role="button" aria-disabled="true" title="ขณะนี้ยังไม่สามารถซื้อได้ค่ะ">เพิ่มใส่ตะกร้า</a>
                                        @endif
                                    @endif
                                @else
                                    @if ($product->isBuyable)    
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            @if(!empty($defaultImage))<input type="hidden" name="image" value="{{ $defaultImage }}">@endif
                                            <input type="hidden" name="name" value="{{ $product->name }}">
                                            <input type="hidden" name="description" value="{{ $product->description }}">
                                            <input type="hidden" name="price" value="{{ $product->price }}">
                                            <button type="submit" class="btn btn-primary btn-lg">เพิ่มใส่ตะกร้า</button>
                                        </form>
                                    @else
                                        <a class="btn btn-primary btn-lg disabled" role="button" aria-disabled="true" title="ขณะนี้ยังไม่สามารถซื้อได้ค่ะ">เพิ่มใส่ตะกร้า</a>
                                    @endif
                                @endif
                            @endif
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="flex card">
        <div class="container p-4">
            {!! $product->description !!}
        </div>
    </section>
@endsection

@section('toast-container')
    {{-- favorite add --}}
    <div id="addFavToast{{$product->id}}" class="toast mb-2 text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img src="{{ asset('icons/oval-interface/heart-filled.svg') }}" class="rounded me-2" width="20" alt="info">
            <strong class="me-auto">เพิ่มเป็นสิ่งที่ถูกใจแล้ว</strong>
            {{-- <small>11 mins ago</small> --}}
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="animU toast-body">
            <a class="no-underline" href="{{ route('product', $product->id) }}">
                <div class="d-flex align-items-center gap-3">
                    <!-- Image Section -->
                    @php
                        $images = json_decode($product->images, true);
                        $image = isset($images[0]) ? $images[0] : null; // Get the first image, or null if not available
                    @endphp
                    @if($image)
                        <img src="{{ asset($image) }}" alt="Product Thumbnail"
                            class="rounded flex-shrink-0"
                            style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <svg class="bd-placeholder-img rounded flex-shrink-0 card" width="50" height="50"
                            xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail"
                            preserveAspectRatio="xMidYMid slice" focusable="false">
                            <rect width="100%" height="100%" fill="#D09CC7" />
                        </svg>
                    @endif
                    <!-- Text Section (Takes up remaining space) -->
                    <div class="flex-grow-1">
                        <h6 class="my-1">{{ Str::limit($product->name, 24) }}</h6>
                        <span class="text-body-secondary">฿{{ $product->prices }}</span>
                    </div>                    
                </div>
            </a>
        </div>
    </div>
    {{-- favortie remove --}}
    <div id="removeFavToast{{$product->id}}" class="toast mb-2 text-bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img src="{{ asset('icons/oval-interface/heart.svg') }}" class="rounded me-2" width="20" alt="info">
            <strong class="me-auto">ลบจากสิ่งที่ถูกใจแล้ว</strong>
            {{-- <small>11 mins ago</small> --}}
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="animU toast-body">
            <a class="no-underline" href="{{ route('product', $product->id) }}">
                <div class="d-flex align-items-center gap-3">
                    <!-- Image Section -->
                    @php
                        $images = json_decode($product->images, true);
                        $image = isset($images[0]) ? $images[0] : null; // Get the first image, or null if not available
                    @endphp
                    @if($image)
                        <img src="{{ asset($image) }}" alt="Product Thumbnail"
                            class="rounded flex-shrink-0"
                            style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <svg class="bd-placeholder-img rounded flex-shrink-0 card" width="50" height="50"
                            xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail"
                            preserveAspectRatio="xMidYMid slice" focusable="false">
                            <rect width="100%" height="100%" fill="#D09CC7" />
                        </svg>
                    @endif
                    <!-- Text Section (Takes up remaining space) -->
                    <div class="flex-grow-1">
                        <h6 class="my-1">{{ Str::limit($product->name, 24) }}</h6>
                        <span class="text-body-secondary">฿{{ $product->prices }}</span>
                    </div>                    
                </div>
            </a>
        </div>
    </div>
@endsection