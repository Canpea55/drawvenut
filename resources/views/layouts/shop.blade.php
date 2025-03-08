@extends('layouts.app')

@section('title', 'ตระกร้าสินค้า')

@section('style')
@endsection

@section('subnavbar-right') 
        @if (auth()->check())
            @if (session('cart'))
                <a class="anim no-underline fw-light bg-success-subtle rounded px-2 py-1" href="{{route('cart')}}">
                    <img src="{{ asset('icons/bag-svgrepo-com.svg') }}" alt="Cart Icon" width="24">
                    <span class="align-middle"> 
                        ตะกร้าสินค้า {{ "(" . count(session('cart')) . ")" }}
                    </span>
                </a>
            @endif

            <a class="anim no-underline fw-light bg-danger-subtle rounded px-2 py-1" href="/cart">
                <img src="{{ asset('icons/heart-svgrepo-com.svg') }}" alt="Heart Icon" width="24">
                <span class="align-middle"> 
                    สิ่งที่ถูกใจ (1)
                </span>
            </a>
        @endif
@endsection

@section('content')
@yield('content')
@endsection

