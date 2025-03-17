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
    ค้นหา
@endsection

@section('foot-script')
    <script type="module">
        $(document).ready(function() {
            var $grid = $('.grid').masonry({
                itemSelector: '.grid-item',
            });

            // Layout Masonry after each image loads
            $('.grid img').each(function() {
                var $img = $(this);
                imagesLoaded($img[0], function() {
                    $grid.masonry('layout');
                });
            });
        });
    </script>
    
    <script>
        const initKeyword = '{{$keyword}}';
        if(initKeyword){
            document.getElementById('live-search').value = '{{$keyword}}';
        } else {
            document.getElementById('live-search').value = 'สินค้าทั้งหมดในร้าน';
        }
    </script>

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
                            bootstrap.Toast.getOrCreateInstance(document.getElementById('removeFavToast' + productID)).show();
                        } else if(response.favorite === 'added'){
                            updateFavoritesCount(1);
                            btnImage.attr('src', '{{ asset("icons/oval-interface/heart-filled.svg") }}');
                            bootstrap.Toast.getOrCreateInstance(document.getElementById('addFavToast' + productID)).show();
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
<div class="row g-3 my-3">
    <div class="col-6 col-md-3 d-none d-md-block">
        <p class="text-primary-emphasis ">📚 กลุ่มสาระการเรียนรู้</p>
        <div>
            <ul class="list-group">
                <a href="{{route('search', ['keyword' => 'คณิต'])}}" class="animS list-group-item list-group-item-action list-group-item-primary d-flex align-items-center gap-1">
                    <div class="display-6">🔢</div>
                    <div>คณิตศาสตร์</div>
                </a>
                <a href="{{route('search', ['keyword' => 'วิทย์'])}}" class="animS list-group-item list-group-item-action list-group-item-primary d-flex align-items-center gap-1">
                    <div class="display-6">🧪</div>
                    <div>วิทยาศาสตร์และเทคโนโลยี</div>
                </a>
                <a href="{{route('search', ['keyword' => 'ไทย'])}}" class="animS list-group-item list-group-item-action list-group-item-primary d-flex align-items-center gap-1">
                    <div class="display-6">🐘</div>
                    <div>ภาษาไทย</div>
                </a>
                <a href="{{route('search', ['keyword' => 'ภาษาต่างประเทศ'])}}" class="animS list-group-item list-group-item-action list-group-item-primary d-flex align-items-center gap-1">
                    <div class="display-6">🌎</div>
                    <div>ภาษาต่างประเทศ</div>
                </a>
                <a href="{{route('search', ['keyword' => 'สังคม'])}}" class="animS list-group-item list-group-item-action list-group-item-primary d-flex align-items-center gap-1">
                    <div class="display-6">👨‍👨‍👧‍👦</div>
                    <div>สังคมศึกษา ศาสนาและวัฒนธรรม</div>
                </a>
                <a href="{{route('search', ['keyword' => 'สุขศึกษา'])}}" class="animS list-group-item list-group-item-action list-group-item-primary d-flex align-items-center gap-1">
                    <div class="display-6">⚽</div>
                    <div>สุขศึกษา พลศึกษา</div>
                </a>
                <a href="{{route('search', ['keyword' => 'ศิลปะ'])}}" class="animS list-group-item list-group-item-action list-group-item-primary d-flex align-items-center gap-1">
                    <div class="display-6">🎨</div>
                    <div>ศิลปศึกษา</div>
                </a>
                <a href="{{route('search', ['keyword' => 'การงาน'])}}" class="animS list-group-item list-group-item-action list-group-item-primary d-flex align-items-center gap-1">
                    <div class="display-6">💼</div>
                    <div>การงานอาชีพ</div>
                </a>
            </ul>  
        </div>      
    </div>
    @if (count($products) > 0)
        <div class="album col-12 col-md-9">
            <div class="container">
                <div class="grid row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 g-3">
                    <div class="toast-container position-fixed bottom-0 end-0 p-2 d-none d-md-block">
                        @foreach ($products as $item)
                            <div id="addFavToast{{$item->id}}" class="toast mb-2 text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header">
                                    <img src="{{ asset('icons/oval-interface/heart-filled.svg') }}" class="rounded me-2" width="20" alt="info">
                                    <strong class="me-auto">เพิ่มเป็นสิ่งที่ถูกใจแล้ว</strong>
                                    {{-- <small>11 mins ago</small> --}}
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="animU toast-body">
                                    <a class="no-underline" href="{{ route('product', $item->id) }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Image Section -->
                                            @php
                                                $images = json_decode($item->images, true);
                                                $image = isset($images[0]) ? $images[0] : null; // Get the first image, or null if not available
                                            @endphp
                                            @if($image)
                                                <img src="{{ Storage::url($image) }}" alt="Product Thumbnail"
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
                                                <h6 class="my-1">{{ Str::limit($item->name, 24) }}</h6>
                                                <span class="text-body-secondary">฿{{ $item->price }}</span>
                                            </div>                    
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        @foreach ($products as $item)
                            <div id="removeFavToast{{$item->id}}" class="toast mb-2 text-bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header">
                                    <img src="{{ asset('icons/oval-interface/heart.svg') }}" class="rounded me-2" width="20" alt="info">
                                    <strong class="me-auto">ลบจากสิ่งที่ถูกใจแล้ว</strong>
                                    {{-- <small>11 mins ago</small> --}}
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="animU toast-body">
                                    <a class="no-underline" href="{{ route('product', $item->id) }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Image Section -->
                                            @php
                                                $images = json_decode($item->images, true);
                                                $image = isset($images[0]) ? $images[0] : null; // Get the first image, or null if not available
                                            @endphp
                                            @if($image)
                                                <img src="{{ Storage::url($image) }}" alt="Product Thumbnail"
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
                                                <h6 class="my-1">{{ Str::limit($item->name, 24) }}</h6>
                                                <span class="text-body-secondary">฿{{ $item->price }}</span>
                                            </div>                    
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @foreach ($products as $item)
                        <div class="col grid-item">
                            <div class="card shadow-sm">
                                @auth
                                <div class="d-flex justify-content-between align-items-start" style="position: absolute; right: 3%; top: 4%; z-index: 1;">
                                    <div class="btn-group gap-1">
                                        @auth
                                        <div class="d-flex justify-content-between align-items-start" style="position: absolute; right: 3%; top: 4%; z-index: 1;">
                                            <div class="btn-group gap-1">
                                                @if (Auth::user()->isAdmin)
                                                    <a href="{{route('product.edit', $item->id)}}" type="button" class="opBtn" title="แก้ไข">
                                                        <img src="{{ asset('icons/oval-interface/edit.svg') }}" alt="edit" width="24">
                                                    </a>
                                                @endif
                                                <form method="POST" action="{{ route('favorite') }}" class="favorite-form" onsubmit="setProductID({{$item->id}});">
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
                                    </div>
                                </div>
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
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
    @else
    <div class="album col-12 col-md-9 d-flex justify-content-center align-items-center">
        <figure class="text-center">
            <blockquote class="blockquote">
              <p>ไม่พบเจอสินค้าใดๆที่ท่านได้ค้นหา</p>
            </blockquote>
            <figcaption>
               <p><small>ขออภัยในความไม่สะดวกนะคะ</small></p>
            </figcaption>
          </figure>
    @endif
</div>
@endsection