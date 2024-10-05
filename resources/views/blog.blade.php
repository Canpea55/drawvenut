@extends('layouts.app')

@section('title', 'บล็อก')

@section('content')
    <h2>บทความทั้งหมด</h2>

    @if (count($blogs) > 0)
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th scope="col">ชื่อบล็อก</th>
                    <th scope="col">เนื้อหา</th>
                    <th scope="col">การเผยแพร่</th>
                    <th scope="col">ควบคุม</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($blogs as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>{{ Str::limit($item->content, 64) }}</td>
                        <td class="text-center">
                            @if ($item->status)
                                <a href="{{ route('change', $item->id) }}" class="btn btn-success">
                                    published
                                </a>
                            @else
                                <a href="{{ route('change', $item->id) }}" class="btn btn-secondary">
                                    unpublished
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('delete', $item->id) }}" class="btn btn-danger"
                                onclick="return confirm('คุณต้องการลบบทความ {{ $item->title }} ไหม?')">
                                ลบ
                            </a>

                            <a href="{{ route('edit', $item->id) }}" class="btn btn-warning">
                                แก้ไข
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $blogs->links() }}

        <br>

        @foreach ($blogs as $item)
            <h4>{{ $item->title }}</h4>
            <p>{{ $item->content }}</p>
            @if ($item->status)
                <p class="text text-success">
                    published
                </p>
            @else
                <p class="text text-danger">
                    unpublished
                </p>
            @endif
            <hr>
        @endforeach
    @else
        <div class="text text-center">ไม่มีบทความในระบบ</div>
    @endif

@endsection
