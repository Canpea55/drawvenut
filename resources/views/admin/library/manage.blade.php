@extends('layouts.app')

@section('title', 'จัดการคลัง')

@section('content')
    <h1 class="my-4 text-center">จัดการคลัง</h1>

    @if (count($library) > 0)
        <table class="table table-bordered align-middle">
            <thead class="text-center">
                <tr>
                    <th scope="col">id</th>
                    <th scope="col">user_id</th>
                    <th scope="col">product_id</th>
                    <th scope="col">created_at</th>
                    <th scope="col">updated_at</th>
                </th>
            </thead>
            <tbody>
                @foreach ($library as $item)
                    <tr>
                        <td class="text-center">{{ $item->id }}</td>
                        <td class="text-center">{{ $item->user_id }}</td>
                        <td class="text-center">{{ $item->product_id }}</td>
                        <td class="text-center">{{ $item->created_at }}</td>
                        <td class="text-center">{{ $item->updated_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $library->links() }}
    @else
        <div class="text text-center">ไม่มีของในคลังใดๆ</div>
    @endif
@endsection
