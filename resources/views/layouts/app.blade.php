<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<style>
    body {
        min-height: 100vh;

        display: flex;
        flex-direction: column;
    }

    .main {
        flex: 1;
    }
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>

    <div id="app">
        {{-- header navbar --}}
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
                            <a class="nav-link" aria-current="page" href="/">หน้าแรก</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('search') }}">สินค้าทั้งหมด</a>
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
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('form') }}">
                                        เขียนบล็อก
                                    </a>

                                    <a class="dropdown-item" href="{{ route('blog') }}">
                                        บล็อกทั้งหมด
                                    </a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        {{-- content --}}
        <div class="container py-4">
            @yield('content')
        </div>

        {{-- footer navbar --}}
        <div class="footer"
            style="
                display:flex;
                boarder-top: 1px solid #8C6C6E;
                justify-content: space-between;
                padding: 2rem;
                
                background-color: #D09CC7;
                color: rgba(0, 0, 0, .8);
                text-align: center;
            ">
            <div>
                © 2024 Drawvenut. ขอสงวนลิขสิทธิ์
            </div>
            <div style="display: flex">
                <a class="nav-link" href="{{ route('about') }}">เกี่ยวกับเรา</a>
            </div>

        </div>
    </div>
</body>

</html>
