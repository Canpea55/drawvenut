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

    @yield('style')
</style>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" 
        integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async>
    </script>

    <div id="app">
        {{-- header navbar --}}
        <header>
            <nav class="shadow navbar navbar-expand-lg" data-bs-theme="white" style="background-color: #D09CC7">
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
                                    <a class="nav-link" href="{{ route('inventory') }}">คลัง</a>
                                </li>

                                <!-- Administrator -->  
                                @if (Auth::user()->isAdmin)
                                    <li class="nav-item btn-group my-auto ms-2">
                                        <a class="btn btn-primary btn-sm" 
                                        href="{{ route('productList') }}">
                                            จัดการสินค้า
                                        </a>
                                        <a type="button" 
                                        class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split my-auto me-2"
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="{{ route('userList') }}">จัดการผู้ใช้งาน</a>
                                        </div>
                                    </li>
                                @endif

                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                            <!--{{ __(key: 'Logout') }}-->ลงชื่อออก
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest

                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        {{-- content --}}
        <main>
            <div class="container py-4">
                @yield('content')
            </div>
        </main>

        {{-- footer--}}
        <footer>
            <div class="container">
                <div class="float-start">
                    <a class="text-start nav-link" href="{{ route('about') }}">© 2024 Drawvenut. ขอสงวนลิขสิทธิ์</a>
                </div>
                <div class="float-end">
                    <a class="nav-link" href="#">กลับขึ้นข้างบนสุด</a>
                </div>
            </div>
        </footer>

        <!-- <footer class="text-body-secondary py-5">
            <div class="container">
                <p class="float-end mb-1">
                    <a href="#">Back to top</a>
                </p>
                <p class="float-start mb-1">Album example is &copy; Bootstrap, but please download and customize it for yourself!</p>
                <p class="mb-0">New to Bootstrap? <a href="/">Visit the homepage</a> or read our <a href="/docs/5.3/getting-started/introduction/">getting started guide</a>.</p>
            </div>
        </footer> -->

    </div>
</body>

</html>
