@extends('layouts.app')

@section('title', 'ฟอร์มสินค้า')

@section('content')
    <h2 class="text text-center py-4"> ฟอร์มสินค้า </h2>
    <form method="POST" action="insert" enctype="multipart/form-data">
        @csrf
        <div class="form-group py-2">
            <label for="name" class="py-2">ชื่อสินค้า</label>
            <input type="text" name="name" class="form-control">
        </div>
        @error('name')
            <div class="ms-2 p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <label for="price" class="py-2">ราคา</label>
            <div class="input-group">
                <input type="number" name="price" class="form-control" placeholder="10">
                <div class="input-group-append">
                    <span class="input-group-text" style="font-family:NotoColorEmoji-Regular;">🪙</span>
                </div>
            </div>
        </div>
        @error('price')
            <div class="ms-2 p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <label for="description" class="py-2">คำอธิบายสินค้า</label>
            <textarea name="description" class="form-control" cols="30" rows="5"></textarea>
        </div>

        <div class="form-group py-2">
            <input class="form-check-input" name="isBuyable" type="checkbox" value="1" {{old('isBuyable') ? 'checked' : ''}}>
            <label for="isBuyable" class="form-check-label">ซื้อได้?</label>
        </div>

        <div class="form-group py-2">
            <input class="form-check-input" name="isDownloadable" type="checkbox" value="1" {{old('isBuyable') ? 'checked' : ''}}>
            <label for="isDownloadable" class="form-check-label">ดาวน์โหลดได้?</label>
        </div>

        <div class="form-group py-2">
            <input class="form-check-input" name="isPublish" type="checkbox" value="1" {{old('isBuyable') ? 'checked' : ''}}>
            <label for="isPublish" class="form-check-label">เผยแพร่?</label>
        </div>

        {{-- <div class="form-group py-2">
            <label for="tags" class="form-label">Tag</label>
            <select id="tags" class="form-select" name="tags[]" multiple>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </select>
        </div> --}}

        <div class="form-group py-2">
            <input type="submit" value="บันทึก" class="btn btn-primary py-2">
        </div>
    </form>
@endsection
