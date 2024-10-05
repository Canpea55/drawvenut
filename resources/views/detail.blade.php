@extends('layouts.app')

@section('title')
{{ $blog->title }}
@endsection

@section('content')
    <h1 class="my-4">{{ $blog->title }}</h1>
    <p>{{ $blog->content }}</p>
@endsection
