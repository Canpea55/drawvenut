@extends('layouts.app')

@section('title', 'แก้ไขบล็อก')

@section('content')
    <h2 class="text text-center py-4"> แก้ไขบล็อก </h2>
    <form method="POST" action="{{route('update', $blog->id)}}">
        @csrf
        <div class="form-group py-2">
            <label for="title" class="py-2">ชื่อบล็อก</label>
            <input type="text" name="title" class="form-control" value="{{$blog->title}}">
        </div>
        @error('title')
            <div class="ms-2 p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <label for="content" class="py-2">เนื้อหาบล็อก</label>
            <textarea name="content" class="form-control" cols="30" rows="5">{{$blog->content}}</textarea>
        </div>
        @error('content')
            <div class="ms-2  p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <input type="submit" value="บันทึกการแก้ไข" class="btn btn-primary py-2">
        <a href="/blog" class="btn btn-success py-2">บทความทั้งหมด</a>
    </form>
@endsection
