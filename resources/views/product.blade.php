@extends('layouts.app')

@section('title')
{{ $product->name }}
@endsection

@section('content')
    <h1 class="my-4">product name = {{ $blog->name }}</h1>
    <p>price = {{ $product->price}}</p>
    <p>product description = {{ $product->description }}</p>
    <p>isBuyable = {{$product->isBuyable}}</p>
    <p>isDownloadable = {{$product->isDownloadable}}</p>
@endsection
