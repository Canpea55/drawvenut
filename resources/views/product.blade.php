@extends('layouts.app')

@section('style')
@endsection

@section('title')
{{ $product->name }}
@endsection

@section('content')
    <h1 class="my-4">product name = {{ $product->name }}</h1>
    <p>price = {{ $product->price}}</p>
    <p>product description = {{ $product->description }}</p>
    <p>isBuyable = {{$product->isBuyable}}</p>
    <p>isDownloadable = {{$product->isDownloadable}}</p>
    <p>isPublish = {{$product->isPublish}}</p>
@endsection
