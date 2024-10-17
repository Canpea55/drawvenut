@extends('layouts.app')

@section('title', 'ฟอร์มสินค้า')

@section('content')
    <h2 class="text text-center py-4"> ฟอร์มสินค้า </h2>
    <form method="POST" action="insert">
        @csrf
        <div class="form-group py-2">
            <label for="title" class="py-2">ชื่อบล็อก</label>
            <input type="text" name="title" class="form-control" placeholder="วันนี้ฉันเป็นเกย์...">
        </div>
        @error('title')
            <div class="ms-2 p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <label for="content" class="py-2">เนื้อหาบล็อก</label>
            <textarea name="content" class="form-control" cols="30" rows="5"></textarea>
        </div>
        @error('content')
            <div class="ms-2  p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <input type="submit" value="บันทึก" class="btn btn-primary py-2">
        <a href="/blog" class="btn btn-success py-2">บทความทั้งหมด</a>
    </form>
@endsection
