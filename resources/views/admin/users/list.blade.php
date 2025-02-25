@extends('layouts.app')

@section('title', 'จัดการสินค้า')

@section('content')
    <div class="py-4 text-center">
        <h1 class="text">จัดการผู้ใช้งาน</h1>
    </div>

    @if (count($users) > 0)
        <table class="table table-hover table-responsive align-middle">
            <thead class="text-center">
                <tr>
                    <th scope="col">ไอดี</th>
                    <th scope="col">ชื่อ</th>
                    <th scope="col">อีเมล</th>
                    <th scope="col">ยืนยันอีเมลเมื่อ</th>
                    <th scope="col">สร้างเมื่อ</th>
                    <th scope="col">อัพเดตล่าสุดเมื่อ</th>
                    <th scope="col">ควบคุม</th>
                </th>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @php
                        if($user->isAdmin){
                            $color = "table-dark";
                        } else {
                            $color = "";
                        }

                    @endphp
                    <tr class="{{$color}}">
                        <td class="text-center">{{ $user->id }}</td>
                        <td class="text-center">{{ $user->name }}</td>
                        <td class="text-center">{{ $user->email}}</td>
                        <td class="text-center">{{ $user->email_verified_at }}</td>
                        <td class="text-center">{{ $user->created_at }}</td>
                        <td class="text-center">{{ $user->updated_at }}</td>
                        <td class="text-center">
                            <a href="{{ route('userEdit', $user->id) }}" class="btn btn-warning">
                                แก้ไข
                            </a>
                            <a href="{{ route('userDelete', $user->id) }}" class="btn btn-danger"
                                onclick="return confirm('ลบ \({{ $user->id }} : {{ $user->name }}\) ไหม?')">
                                ลบ
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
    @else
        <div class="text text-center">ไม่มีสินค้าในระบบ</div>
    @endif
@endsection
