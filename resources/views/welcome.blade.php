@extends('layouts.app')

@section('title', 'หน้าแรก')

@section('content')
    <h1 class="my-4">บทความล่าสุด</h1>
    @foreach ($blogs as $item)
        <h2>{{$item->title}}</h2>
        <p>{{$item->content}}</p>
        <a href="/detail/{{$item->id}}">อ่านเพิ่มเติม</a>
        <hr>
    @endforeach
@endsection
