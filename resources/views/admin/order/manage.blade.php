@extends('layouts.app')

@section('title', 'จัดการออร์เดอร์')

@section('head-script')
    <script>
        function getCookie(name) {
            let cookies = document.cookie.split("; ");
            for (let cookie of cookies) {
                let [key, value] = cookie.split("=");
                if (key === name) return value;
            }
            return null;
        }

        document.addEventListener("DOMContentLoaded", function () {
            // const urlParams = new URLSearchParams(window.location.search);
            // const firstParam = Array.from(urlParams.keys())[0];
            let savedFilter = getCookie("orderFilter");

            if(savedFilter)
            {
                document.querySelectorAll('.order-filter').forEach(form => {
                    form.style.display = 'none';
                });

                switch(savedFilter){
                    case 'orders':
                        document.getElementById("filterLabel").innerText = "ทั้งหมด"
                        document.getElementById("all").style.display = "block";
                        document.getElementById("all").scrollIntoView();
                        document.getElementById("orderFilter").value = "all";
                        break;
                    case 'pending':
                        document.getElementById("filterLabel").innerText = "รอโอนเงิน"
                        document.getElementById("pending").style.display = "block";
                        document.getElementById("pending").scrollIntoView();
                        document.getElementById("orderFilter").value = "pending";
                        break;
                    case 'success':
                        document.getElementById("filterLabel").innerText = "เรียบร้อย"
                        document.getElementById("success").style.display = "block";
                        document.getElementById("success").scrollIntoView();
                        document.getElementById("orderFilter").value = "success";
                        break;                 
                    default:
                        document.getElementById("filterLabel").innerText = "ทั้งหมด"
                        document.getElementById("all").style.display = "block";
                }
            } else {

            }
        });
    </script>
    <script>
        function changeOrderFilter() {
            let selectedFilter = document.getElementById("orderFilter").value;

            document.cookie = `orderFilter=${selectedFilter}; path=/; max-age=${60 * 60}`;

            document.querySelectorAll('.order-filter').forEach(form => {
                form.style.display = 'none';
            });

            switch(selectedFilter){
                case 'all':
                    document.getElementById("filterLabel").innerText = "ทั้งหมด"
                    document.getElementById("all").style.display = "block";
                    document.getElementById("all").scrollIntoView();
                    break;
                case 'pending':
                    document.getElementById("filterLabel").innerText = "รอโอนเงิน"
                    document.getElementById("pending").style.display = "block";
                    document.getElementById("pending").scrollIntoView();
                    break;
                case 'success':
                    document.getElementById("filterLabel").innerText = "เรียบร้อย"
                    document.getElementById("success").style.display = "block";
                    document.getElementById("success").scrollIntoView();
                    break;                 
                default:
                    document.getElementById("filterLabel").innerText = "ทั้งหมด"
                    document.getElementById("all").style.display = "block";
            }
        }
    </script>
@endsection

@section('style')
    .underline-none {
        text-decoration: none;
    }
@endsection

@section('content')

    @if (count($orders) > 0)
        <h1 class="text-info-emphasis">รอตรวจสอบบิล</h1>
        <div class="row row-cols-1 row-cols-md-2 g-3" data-masonry='{"percentPosition": true }'>
            @if ($submitted->isNotEmpty())
                @foreach ($submitted as $item)
                <div class="col-md-6">
                    <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                        <div class="col p-4 d-flex flex-column position-static">
                            <a class="underline-none" href="{{route('order', [$item->id])}}">
                                <strong class="d-inline-block mb-2 text-primary-emphasis">{{$item->id}}</strong>
                                <h3 class="mb-0">฿{{$item->total}}</h3>
                                <div class="mb-1 text-body-secondary">{{ convertTimezone($item->created_at)  }}</div>
                            </a>
                            <p class="card-text mb-auto"></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <form method="POST" action="{{ route('order.approve') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$item->id}}">
                                        <input 
                                            type="submit"
                                            value="อนุมัติ"
                                            class="btn btn-lg btn-success"
                                            onclick="return confirm('ยืนยันว่าตรวจสอบเรียบร้อยแล้วอนุมัติออร์เดอร์ที่ {{ $item->id }} ยอดรวม ฿{{$item->total}} ไหม?')"
                                        >
                                    </form>
                                </div>
                                <small class="text-body-secondary">{{ calculateDuration($item->updated_at, now()) }}ที่แล้ว</small>
                            </div>
                        </div>
                        <div class="col-auto d-none d-lg-block">
                            <a class="underline-none" href="{{route('order', [$item->id])}}">
                                @if ($item->receipt)
                                    {{-- with image --}}
                                    <img 
                                        width="200" height="250"
                                        src="{{ Storage::url($item->receipt) }}" 
                                        alt="Product Thumbnail" 
                                        class="card-img-top"
                                        style="width: 200px; height: 250px; object-fit: cover;"
                                    >
                                @else
                                    {{-- no image --}}
                                    <svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" role="img"
                                        aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false">
                                        <rect width="100%" height="100%" fill="#d09cc7" />
                                    </svg>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <p class="">
                    ไม่มีออร์เดอร์ที่ต้องตรวจ
                </p>
            @endif
        </div>
        {{ $submitted->links() }}

        <!-- Filters -->
        <div class="d-flex justify-content-between mt-5">
            <div class="align-self-center">
                <h1 id="filterLabel">ทั้งหมด</h1>
            </div>

            <div class="align-self-center">
                <form>
                    <select class="form-select" id="orderFilter" name="orderFilter" onchange="changeOrderFilter()">
                        <option value="all">ทั้งหมด</option>
                        <option value="pending">รอโอนเงิน</option>
                        <option value="success">สำเร็จ</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <div id="all" class="row order-filter mx-2" style="display: block">
                <table class="table table-bordered align-middle">
                    <thead class="text-center">
                        <tr>
                            <th scope="col">id</th>
                            <th scope="col">user_id</th>
                            <th scope="col">total</th>
                            <th scope="col">status</th>
                            <th scope="col">receipt</th>
                            <th scope="col">created_at</th>
                            <th scope="col">updated_at</th>
                        </th>
                    </thead>
                    <tbody>
                        @foreach ($orders as $item)
                        <tr>
                            <td class="text-center"><a href="{{route('order', [$item->id])}}">{{ $item->id }}</a></td>
                            <td class="text-center">{{ $item->user_id }}</td>
                            <td class="text-center">{{ $item->total }}</td>
                            <td class="text-center">{{ $item->status }}</td>
                            <td class="text-center">{{ $item->receipt }}</td>
                            <td class="text-center">{{ convertTimezone($item->created_at) }}</td>
                            <td class="text-center">{{ convertTimezone($item->updated_at) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $orders->links() }}
            </div>
            <div id="pending" class="row order-filter" style="display: none">
                <table class="table table-bordered align-middle">
                    <thead class="text-center">
                        <tr>
                            <th scope="col">id</th>
                            <th scope="col">user_id</th>
                            <th scope="col">total</th>
                            <th scope="col">status</th>
                            <th scope="col">receipt</th>
                            <th scope="col">created_at</th>
                            <th scope="col">updated_at</th>
                        </th>
                    </thead>
                    <tbody>
                        @foreach ($pending as $item)
                        <tr>
                            <td class="text-center"><a href="{{route('order', [$item->id])}}">{{ $item->id }}</a></td>
                            <td class="text-center">{{ $item->user_id }}</td>
                            <td class="text-center">{{ $item->total }}</td>
                            <td class="text-center">{{ $item->status }}</td>
                            <td class="text-center">{{ $item->receipt }}</td>
                            <td class="text-center">{{ convertTimezone($item->created_at) }}</td>
                            <td class="text-center">{{ convertTimezone($item->updated_at) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $pending->links() }}
            </div>
            <div id="success" class="row order-filter" style="display: none">
                <table class="table table-bordered align-middle">
                    <thead class="text-center">
                        <tr>
                            <th scope="col">id</th>
                            <th scope="col">user_id</th>
                            <th scope="col">total</th>
                            <th scope="col">status</th>
                            <th scope="col">receipt</th>
                            <th scope="col">created_at</th>
                            <th scope="col">updated_at</th>
                        </th>
                    </thead>
                    <tbody>
                        @foreach ($success as $item)
                        <tr>
                            <td class="text-center"><a href="{{route('order', [$item->id])}}">{{ $item->id }}</a></td>
                            <td class="text-center">{{ $item->user_id }}</td>
                            <td class="text-center">{{ $item->total }}</td>
                            <td class="text-center">{{ $item->status }}</td>
                            <td class="text-center">{{ $item->receipt }}</td>
                            <td class="text-center">{{ convertTimezone($item->created_at) }}</td>
                            <td class="text-center">{{ convertTimezone($item->updated_at) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $success->links() }}
            </div>
        </div>
    @else
        <div class="text text-center">ไม่มีของในคลังใดๆ</div>
    @endif
@endsection
