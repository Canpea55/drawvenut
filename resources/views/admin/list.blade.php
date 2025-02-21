@extends('layouts.app')

@section('title', 'คลังสินค้า')

@section('content')
    <h2>คลังสินค้า</h2>

    <a class="btn btn-primary my-2" href="{{route("productCreate")}}">เพิ่มสินค้า</a>

    @if (count($products) > 0)
        <table class="table table-bordered align-middle">
            <thead class="text-center">
                <tr>
                    <th scope="col">ภาพปก</th>
                    <th scope="col">ชื่อสินค้า</th>
                    <th scope="col">คำอธิบาย</th>
                    <th scope="col">ราคา</th>
                    <th scope="col">ควบคุม</th>
                </th>
            </thead>
            <tbody>
                @foreach ($products as $item)
                    <tr>
                        <td class="text-center ">
                            @php
                                $images = json_decode($item->images, true);
                            @endphp

                            <div class="flex gap-2">
                                @if(is_array($images) && count($images) > 0)
                                    @foreach($images as $image)
                                        <img src="{{ Storage::url($image) }}" 
                                            alt="Product Image" 
                                            width="100" 
                                            class="rounded border">
                                    @endforeach
                                @else
                                    <p>No Images Available</p>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">{{ $item->name }}</td>
                        <td class="text-center">{{ Str::limit($item->description, 64) }}</td>
                        <td class="text-center">{{ $item->price }}</td>
                        <td class="text-center">
                            <a href="{{ route('productDelete', $item->id) }}" class="btn btn-danger"
                                onclick="return confirm('ลบ {{ $item->name }} ไหม?')">
                                ลบ
                            </a>

                            <a href="{{ route('productEdit', $item->id) }}" class="btn btn-warning">
                                แก้ไข
                            </a>

                            @if ($item->isPublish)
                                <a href="{{ route('productToggle', $item->id) }}" class="btn btn-success">
                                    เผยแพร่
                                </a>
                            @else
                                <a href="{{ route('productToggle', $item->id) }}" class="btn btn-secondary">
                                    ไม่เผยแพร่
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $products->links() }}
    @else
        <div class="text text-center">ไม่มีสินค้าในระบบ</div>
    @endif
@endsection
