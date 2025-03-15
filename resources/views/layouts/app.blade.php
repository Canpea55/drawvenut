<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<style type="text/css">
    header {
        position: relative;
        z-index: 1;
    }
    main {
        position: relative;
        min-height: 80vh;
        z-index: 0;
    }
    footer {
        z-index: 1;
    }

    .no-underline {
        position: relative;
        text-decoration: none;
        color: inherit;
    }

    /* hover animation */
    .anim {
        transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.5s ease;
        opacity: 0.9;
    }
    .anim:hover {
        transform: translateY(-2px); /* Moves card up slightly */
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1); /* Adds shadow */
        opacity: 1;
    }


    /* scroll bar */
    ::-webkit-scrollbar{
        width: 10px;
    }
    
    ::-webkit-scrollbar-track-piece{
        background-color: #FFF;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb{
        background-color: #CBCBCB;
        outline: 2px solid #FFF;
        outline-offset: -2px;
        border: .1px solid #B7B7B7;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover{
        background-color: #909090;
    }   

    /* btn */
    .opBtn {
        transition: transform 0.3s ease, opacity 0.5s ease;
        opacity: 0.5;
        will-change: transform, opacity, box-shadow;
    }
    .opBtn:hover {
        transform: translateY(-5px);
        opacity: 1;
    }

    .unselectable {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    @yield('style')
</style>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- title -->
    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link href="{{ asset('css/voxybun-toggle.css') }}" rel="stylesheet">
    @vite('resources/sass/app.scss')
    @vite('resources/js/app.js')

    <!-- Head Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @yield('head-script')
</head>

<body>
    <div id="app">
        {{-- header navbar --}}
        <header>
            <!-- main navbar -->
            <nav class="shadow-sm navbar navbar-expand-lg bg-primary bg-gradient" data-bs-theme="white">
                <div class="position-absolute top-50 start-50 translate-middle">
                    <div class="d-flex align-items-center gap-2 ms-1 align-middle d-none d-md-block position-relative" style="min-width: 30vw">
                        <form action="{{ route('search') }}" method="GET" class="w-100">
                            <input class="form-control text-center" 
                                placeholder="" 
                                type="search" 
                                id="live-search"
                                name="keyword"
                                aria-label="Search"
                                onkeydown="if(event.key === 'Enter') this.form.submit();">
                        </form>
                        <div id="searchplaceholders" class="position-absolute top-50 start-50 translate-middle text-center unselectable">
                            <div id="searchplaceholder1" class="d-none">🔎ใบงานวันแม่</div>
                            <div id="searchplaceholder2" class="d-none">🔎สแตนดี้รูปหมา</div>
                            <div id="searchplaceholder3" class="d-none">🔎รายงานวิจัยวิทยานิพล</div>
                        </div>
                        <ul id="search-results" class="list-group position-absolute w-100 mt-0" style="top: 100%; z-index: 1025;"></ul>
                    </div>
                </div>
                
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ url('images/logo-long.png') }}" alt="Logo" style="width:150px; height:48px;"
                            class="d-inline-block">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto my-2">

                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto my-2">
                            
                            <li class="nav-item my-1 mx-1">
                                <a class="nav-link" aria-current="page" href="/">ร้านค้า</a>
                            </li>
                            
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item my-1 mx-1">
                                        <a class="nav-link" href="{{ route('login') }}">เข้าสู่ระบบ</a>
                                    </li>
                                @endif

                                @if (Route::has('register'))
                                    <li class="nav-item my-1 mx-1">
                                        <a class="btn btn-outline-success mx-2" type="button"
                                            href="{{ route('register') }}">สมัครสมาชิก</a>
                                    </li>
                                @endif
                            @else
                                <!-- Customer -->
                                <li class="nav-item my-1 mx-1">
                                    <a class="nav-link" href="{{ route('library') }}">คลัง</a>
                                </li>

                                <li class="nav-item dropdown my-1 mx-1">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item anim" href="{{ route('orders') }}">
                                                <img src="{{ asset('icons/oval-interface/square-sticker.svg') }}" alt="Add Icon" width="16"> คำสั่งซื้อของฉัน
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @if (Auth::user()->isAdmin)
                                            <li>
                                                <a class="dropdown-item anim" href="{{ route('order.manage') }}">
                                                    <img src="{{ asset('icons/oval-interface/square-sticker.svg') }}" alt="Add Icon" width="16"> จัดการออร์เดอร์
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item anim" href="{{ route('product.manage') }}">
                                                    <img src="{{ asset('icons/oval-interface/edit.svg') }}" alt="Add Icon" width="16"> จัดการสินค้า
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item anim" href="{{ route('users') }}">
                                                    <img src="{{ asset('icons/oval-interface/3users.svg') }}" alt="Add Icon" width="16"> จัดการผู้ใช้งาน
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item anim" href="{{ route('library.manage') }}">
                                                    <img src="{{ asset('icons/oval-interface/grid-aspect-ratio.svg') }}" alt="Add Icon" width="16"> จัดการคลัง
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item anim" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                                <img src="{{ asset('icons/oval-interface/power.svg') }}" alt="Add Icon" width="16"> ลงชื่อออก
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- sub navbar -->
            <div class="container my-3 d-flex justify-content-between">                
                <div class="d-flex align-items-center gap-2 ms-1 align-middle">
                    @auth
                        @if (Auth::user()->isAdmin)
                            <a class="opBtn no-underline px-2 py-1" href="{{route("order.manage")}}" title="จัดการออร์เดอร์">
                                <img src="{{ asset('icons/oval-interface/square-sticker.svg') }}" alt="ordermangeicon" width="24">
                            </a>
                            <a class="opBtn no-underline px-2 py-1" href="{{route("product.manage")}}" title="จัดการสินค้า">
                                <img src="{{ asset('icons/oval-interface/edit.svg') }}" alt="productmanageicon" width="24">
                            </a>
                            <a class="opBtn no-underline px-2 py-1" href="{{route("users")}}" title="จัดการผู้ใช้งาน">
                                <img src="{{ asset('icons/oval-interface/3users.svg') }}" alt="usermanageicon" width="24">
                            </a>
                            <a class="opBtn no-underline px-2 py-1" href="{{route("library.manage")}}" title="จัดการคลัง">
                                <img src="{{ asset('icons/oval-interface/grid-aspect-ratio.svg') }}" alt="librarymanageicon" width="24">
                            </a>
                        @endif
                    @endauth
                    @yield(section: 'subnavbar-left')
                </div>
                
                <div class="d-flex align-items-center gap-2 me-1 align-middle">
                    @yield('subnavbar-right')
                </div>
            </div>
        </header>

        {{-- content --}}
        <main>
            <div class="container">
                @yield('content')
            </div>
        </main>

        {{-- footer--}}
        <footer class="mt-3 p-4 border-top bg-primary">
            <div class="container align-items-center d-flex justify-content-between ">
                <a class="no-underline" href="{{ route('about') }}">© 2024 Drawvenut. ขอสงวนลิขสิทธิ์</a>
                <a class="no-underline" href="#">กลับขึ้นข้างบนสุด</a>
            </div>
            @yield('footer')
        </footer>
    </div>
</body>

<!-- Foot Scripts -->
@yield('foot-script')

<script>
$(document).ready(function () {
    let activeIndex = -1;
    let typingTimer;
    const typingDelay = 250;
    const $input = $("#live-search");
    const $resultsList = $("#search-results");

    // Handle keydown for navigation
    $input.on("keydown", function(e){
        let $results = $resultsList.find("a");
        let resultsCount = $results.length;

        if(e.key === "ArrowDown"){
            e.preventDefault();
            if(resultsCount > 0){
                activeIndex = (activeIndex + 1) % resultsCount;
                $results.removeClass("active");
                $results.eq(activeIndex).addClass("active");
            }
        }
        else if(e.key === "ArrowUp"){
            e.preventDefault();
            if(resultsCount > 0){
                activeIndex = activeIndex <= 0 ? resultsCount - 1 : activeIndex - 1;
                $results.removeClass("active");
                $results.eq(activeIndex).addClass("active");
            }
        }
        else if(e.key === "Enter"){
            let $activeItem = $results.filter(".active");
            if($activeItem.length){
                e.preventDefault();
                window.location.href = $activeItem.attr("href");
            }
        }
        // Do not reset activeIndex here; let it persist so that the AJAX callback can reapply it.
    });

    // Handle keyup/blur to trigger live search
    $input.on("keyup blur", function () {
        clearTimeout(typingTimer);
        let query = $input.val().trim();

        typingTimer = setTimeout(function(){
            if(query.length > 0){
                $.ajax({
                    url: "{{ route('search.live') }}",
                    method: "GET",
                    data: { query: query },
                    success: function (data) {
                        let results = '';
                        // Build product suggestions if present
                        if (data.products && data.products.length > 0) {
                            $.each(data.products, function (i, product) {
                                results += `
                                    <a href="/product/${product.id}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                        ${product.image ? `<img src="${product.image}" alt="${product.name}" class="rounded flex-shrink-0 border" style="width: 50px; height: 100%; object-fit: cover;">` : ''}
                                        <div>
                                            <div>${product.name}</div>
                                            <small class="text-success">฿${product.price}</small>
                                        </div>
                                    </a>
                                `;
                            });
                        }
                        // Append tag suggestions if present
                        if (data.tags && data.tags.length > 0) {
                            $.each(data.tags, function (i, tag) {
                                results += `
                                    <a href="${tag.url}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                        <div>
                                            <div>${tag.name}</div>
                                        </div>
                                    </a>
                                `;
                            });
                        }

                        // Update results list
                        $resultsList.html(results);

                        // If activeIndex is still valid, reapply the active class
                        let $newResults = $resultsList.find("a");
                        if(activeIndex >= 0 && activeIndex < $newResults.length) {
                            $newResults.removeClass("active");
                            $newResults.eq(activeIndex).addClass("active");
                        } else {
                            activeIndex = -1; // Reset if no valid selection exists
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("Live search error:", error);
                        $resultsList.empty();
                    }
                });
            } else {
                $resultsList.empty();
            }
        }, typingDelay);
    });
});
</script>

<script>
    let currentIndex = 1;
    const totalPlaceholders = 3;
    const input = document.querySelector('input[type="search"]');
    let intervalId;

    function updatePlaceholder() {
        const placeholder = document.getElementById(`searchplaceholder${currentIndex}`);
        input.placeholder = placeholder.textContent;
        currentIndex = currentIndex >= totalPlaceholders ? 1 : currentIndex + 1;
    }

    function startPlaceholderCycle() {
        updatePlaceholder();
        intervalId = setInterval(updatePlaceholder, 3000);
    }

    // Initial start
    startPlaceholderCycle();

    // Stop on focus
    input.addEventListener('focus', () => {
        clearInterval(intervalId);
        input.placeholder = '';
    });

    // Restart on blur
    input.addEventListener('blur', () => {
        if (!input.value) {
            startPlaceholderCycle();
        }
    });
</script>

</html>
