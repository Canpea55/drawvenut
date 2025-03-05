@extends('layouts.app')

@section('title', 'จัดการสินค้า')

@section('content')
    <h1 class="my-4 text-center">จัดการสินค้า</h1>

    <a class="btn btn-primary my-2" href="{{route("product.create")}}">เพิ่มสินค้า</a>

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
                        <td class="text-center">
                            @php
                                $images = json_decode($item->images, true);
                            @endphp

                            <div class="flex gap-2">
                                @if(is_array($images) && count($images) > 0)
                                    @php
                                        $image = $images[0];
                                    @endphp    
                                    <img src="{{ Storage::url($image) }}" 
                                        alt="Product Image" 
                                        width="100" 
                                        class="rounded border">
                                @else
                                    No Images Available
                                @endif
                            </div>
                        </td>
                        <td class="text-center">{{ $item->name }}</td>
                        <td class="text-center">{{ Str::limit($item->description, 64) }}</td>
                        <td class="text-center">{{ $item->price }}</td>
                        <td class="text-center">
                            <a href="{{ route('product.delete', $item->id) }}" class="btn btn-danger"
                                onclick="return confirm('ลบ {{ $item->name }} ไหม?')">
                                ลบ
                            </a>

                            <a href="{{ route('product.edit', $item->id) }}" class="btn btn-warning">
                                แก้ไข
                            </a>

                            @if ($item->isPublish)
                                <a href="{{ route('product.toggle', $item->id) }}" class="btn btn-success">
                                    เผยแพร่
                                </a>
                            @else
                                <a href="{{ route('product.toggle', $item->id) }}" class="btn btn-secondary">
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
