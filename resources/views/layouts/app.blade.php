<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<style type="text/css">
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    footer {
        display: flex;
        border-top: 1px solid rgba(0, 0, 0, .25);
        justify-content: space-between;
        padding: 2rem;

        background-color: #D09CC7;
        color: rgba(0, 0, 0, .8);
        text-align: center;
    }

    main {
        min-height: 100vh;
    }

    @font-face {
        font-family: NotoColorEmoji-Regular;
        src: url('{{ public_path('fonts/NotoColorEmoji-Regular.tff') }}');
    }

    .no-underline {
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

    <!-- Fonts -->
    

    <!-- Head Scripts -->
    @vite('node_modules/bootstrap/dist/css/bootstrap.min.css')
    @yield('head-script')
</head>

<body>
    <div id="app">
        {{-- header navbar --}}
        <header>
            <!-- main navbar -->
            <nav class="shadow-sm navbar navbar-expand-lg" data-bs-theme="white" style="background-color: #D09CC7">
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
                        <ul class="navbar-nav me-auto">

                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto ">
                            
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="/">ร้านค้า</a>
                            </li>
                            
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">เข้าสู่ระบบ</a>
                                    </li>
                                @endif

                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="btn btn-outline-success mx-2" type="button"
                                            href="{{ route('register') }}">สมัครสมาชิก</a>
                                    </li>
                                @endif
                            @else
                                <!-- Customer -->
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('library') }}">คลัง</a>
                                </li>

                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <ul class="dropdown-menu">
                                        <!-- Administrator -->  
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
                                <img src="{{ asset('icons/oval-interface/square-sticker.svg') }}" alt="Add Icon" width="24">
                            </a>
                            <a class="opBtn no-underline px-2 py-1" href="{{route("product.manage")}}" title="จัดการสินค้า">
                                <img src="{{ asset('icons/oval-interface/edit.svg') }}" alt="Add Icon" width="24">
                            </a>
                            <a class="opBtn no-underline px-2 py-1" href="{{route("users")}}" title="จัดการผู้ใช้งาน">
                                <img src="{{ asset('icons/oval-interface/3users.svg') }}" alt="Add Icon" width="24">
                            </a>
                            <a class="opBtn no-underline px-2 py-1" href="{{route("library.manage")}}" title="จัดการคลัง">
                                <img src="{{ asset('icons/oval-interface/grid-aspect-ratio.svg') }}" alt="Add Icon" width="24">
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
        <footer class="mt-3">
            <div class="container">
                <div class="float-start lead">
                    <a class="text-start nav-link" href="{{ route('about') }}">© 2024 Drawvenut. ขอสงวนลิขสิทธิ์</a>
                </div>
                <div class="float-end">
                    <a class="nav-link" href="#">กลับขึ้นข้างบนสุด</a>
                </div>
            </div>
            @yield('footer')
        </footer>
    </div>
</body>

<!-- Foot Scripts -->
@vite(['resources/sass/app.scss', 'resources/js/app.js'])
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.min.js"></script>
@yield('foot-script')

</html>
