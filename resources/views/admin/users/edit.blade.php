@extends('layouts.app')

@section('title', 'แก้ไขสินค้า')

@section('content')
    <form method="POST" action="{{ route('userUpdate', $user->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group py-4 text-center">
            <h1 class="text"> แก้ไขรายละเอียดบัญชีผู้ใช้ </h1>
        </div>

        <div class="form-group py-4">
            <p class=""> ไอดี : {{$user->id}} </p>
            <p class=""> ยืนยันอีเมลเมื่อ : {{$user->email_verified_at}} </p>
            <p class=""> สร้างเมื่อ : {{$user->created_at}} </p>
            <p class=""> อัพเดตล่าสุดเมื่อ : {{$user->updated_at}} </p>
        </div>
        
        <div class="form-group py-2">
            <label for="name" class="py-2">ชื่อ (name)</label>
            <input type="text" name="name" class="form-control" value="{{$user->name}}" required>
        </div>
        @error('name')
            <div class="my-1">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <label for="email" class="py-2">อีเมล (email)</label>
            <input type="text" name="email" class="form-control" value="{{$user->email}}" required>
        </div>
        @error('email')
            <div class="my-1">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <label for="password" class="py-2">รหัสผ่านใหม่</label>
            <input type="text" name="password" class="form-control">

            <label for="password-confirmation" class="py-2">ยืนยันรหัสผ่านรหัสผ่านใหม่</label>
            <input type="text" name="password" class="form-control">
        </div>
        @error('password')
            <div class="my-1">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <input type="submit" value="บันทึก" class="btn btn-primary py-2">
        </div>
    </form>
@endsection
