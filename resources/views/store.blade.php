@extends('layouts.app')

@section('style')
@endsection

@section('title')
ร้านค้า
@endsection

@section('content')
    
<h2>สินค้าทั้งหมด</h2>

@if (count($products) > 0)
    <table class="table table-bordered">
        <thead class="text-center">
            <tr>
                <th scope="col">ชื่อสินค้า</th>
                <th scope="col">คำอธิบาย</th>
                <th scope="col">ราคา</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ Str::limit($item->description, 64) }}</td>
                    <td>{{ $item->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="text text-center">ไม่มีบทความในระบบ</div>
@endif

@endsection
