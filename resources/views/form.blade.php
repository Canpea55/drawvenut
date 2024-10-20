@extends('layouts.app')

@section('title', 'ฟอร์มสินค้า')

@section('content')
    <h2 class="text text-center py-4"> ฟอร์มสินค้า </h2>
    <form method="POST" action="insert">
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
            <input type="number" name="price" class="form-control" placeholder="10">
        </div>

        <div class="form-group py-2">
            <label for="description" class="py-2">คำอธิบายสินค้า</label>
            <textarea name="description" class="form-control" cols="30" rows="5"></textarea>
        </div>
        @error('description')
            <div class="ms-2  p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <input class="form-check-input" name="isBuyable" type="checkbox" value=""></input>
            <label for="isBuyable" class="form-check-label">อณุญาติให้ลูกค้าสามารถซื้อได้?</label>
        </div>

        <div class="form-group py-2">
            <input class="form-check-input" name="isDownloadable" type="checkbox" value=""></input>
            <label for="isDownloadable" class="form-check-label">อณุญาติให้ลูกค้าสามารถดาวน์โหลดได้?</label>
        </div>

        <div class="form-group py-2">
            <input class="form-check-input" name="isPublish" type="checkbox" value=""></input>
            <label for="isPublish" class="form-check-label">เผยแพร่?</label>
        </div>

        <div class="form-group py-2">
            <input type="submit" value="บันทึก" class="btn btn-primary py-2">
        </div>
    </form>
@endsection
