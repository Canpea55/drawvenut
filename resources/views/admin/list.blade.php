@extends('layouts.app')

@section('title', 'บล็อก')

@section('content')
    <h2>สินค้าในคลัง</h2>

    <a class="btn btn-primary my-2" href="{{route("productCreate")}}">เพิ่มสินค้า</a>

    @if (count($products) > 0)
        <table class="table table-bordered align-middle">
            <thead class="text-center">
                <tr>
                    <th scope="col">ภาพปก</th>
                    <th scope="col">ชื่อสินค้า</th>
                    <th scope="col">คำอธิบาย</th>
                    <th scope="col">ราคา</th>
                    <th scope="col">ระดับการเผยแพร่</th>
                    <th scope="col">เครื่องมือ</th>
                </th>
            </thead>
            <tbody>
                @foreach ($products as $item)
                    <tr>
                        <td>
                            @if ($item->image)
                                <img src="{{ Storage::url($item->image) }}" alt="Product Image" width="150">
                            @else
                                <p>No Image Available</p>
                            @endif
                        </td>
                        <td>{{ $item->name }}</td>
                        <td>{{ Str::limit($item->description, 64) }}</td>
                        <td class="text-center">{{ $item->price }}</td>
                        <td class="text-center">
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
                        <td class="text-center">
                            <a href="{{ route('productDelete', $item->id) }}" class="btn btn-danger"
                                onclick="return confirm('ลบ {{ $item->name }} ไหม?')">
                                ลบ
                            </a>

                            <a href="{{ route('productEdit', $item->id) }}" class="btn btn-warning">
                                แก้ไข
                            </a>
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
