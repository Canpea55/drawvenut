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
            var $grid = $('.grid').masonry({
                itemSelector: '.grid-item',
            });
            $grid.masonry();

            let savedFilter = getCookie("orderFilter");

            if(savedFilter) {
                document.getElementById("orderFilter").value = savedFilter;
                changeOrderFilter();
            }
        });

        function changeOrderFilter() {
            let selectedFilter = document.getElementById("orderFilter").value;

            document.cookie = `orderFilter=${selectedFilter}; path=/; max-age=${60 * 60}`;

            document.querySelectorAll('.order-filter-row').forEach(row => {
                row.style.display = 'none';
            });

            document.querySelectorAll(`.order-filter-row[data-status="${selectedFilter}"]`).forEach(row => {
                row.style.display = 'table-row';
            });

            let filterLabel = {
                'all': 'ทั้งหมด',
                'pending': 'รอโอนเงิน',
                'success': 'เรียบร้อย',
                'cancelled': 'ยกเลิกแล้ว'
            };

            document.getElementById("filterLabel").innerText = filterLabel[selectedFilter] || 'ทั้งหมด';
        }

        function showCancelForm(order_id) {
            document.getElementById("cancelForm" + order_id).style.display = "block";
            var $grid = $('.grid').masonry({
                itemSelector: '.grid-item',
            });
            $grid.masonry();
        }

        function updateReason(order_id) {
            if(document.getElementById("cancelReason" + order_id).value === "อื่นๆ : ") {
                document.getElementById("ectReason" + order_id).style.display = "block";
                document.getElementById("cancelForm" + order_id).reason.value = document.getElementById("cancelReason" + order_id).value + document.getElementById("ectReason" + order_id).value;
            } else {
                document.getElementById("ectReason" + order_id).style.display = "none";
                document.getElementById("cancelForm" + order_id).reason.value = document.getElementById("cancelReason" + order_id).value;
            }
            var $grid = $('.grid').masonry({
                itemSelector: '.grid-item',
            });
            $grid.masonry();
        }
    </script>
@endsection

@section('content')
    @if (count($orders) > 0)
        <div class="pt-4">
            <h1 class="text-info-emphasis">รอตรวจสอบ</h1>
        </div>
        
        <div class="grid row row-cols-1 row-cols-md-2 g-3">
            @if ($submitted->isNotEmpty())
                @foreach ($submitted as $item)
                <div class="grid-item col-md-6" onmouseover="updateReason({{$item->id}})">
                    <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                        <div class="col p-4 d-flex flex-column position-static">
                            <a class="no-underline" href="{{route('order', [$item->id])}}">
                                <strong class="d-inline-block mb-2 text-primary-emphasis">{{$item->id}}</strong>
                                <h3 class="mb-0">฿{{$item->total}}</h3>
                                <div class="mb-1 text-body-secondary">{{ convertTimezone($item->created_at)  }}</div>
                                <small class="text-body-secondary">{{ calculateDuration($item->updated_at, now()) }}ที่แล้ว</small>
                            </a>
                            <p class="card-text mb-auto"></p>
                            <form id="approveForm" method="POST" action="{{ route('order.approve') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{$item->id}}">
                            </form>
                            <div style="height: 100px;"></div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button form="approveForm" type="submit" class="btn btn-lg btn-success" onclick="return confirm('ยืนยันว่าตรวจสอบเรียบร้อยแล้วอนุมัติออร์เดอร์ที่ {{ $item->id }} ยอดรวม ฿{{$item->total}} ไหม?')">อนุมัติ</button>
                                    <button id="cancel" class="btn btn-danger btn-lg" onclick="showCancelForm({{$item->id}}); updateReason({{$item->id}}); return false;">ยกเลิกคำสั่งซื้อ</button>
                                </div>
                            </div>
                            <form id="cancelForm{{$item->id}}" method="POST" action="{{route('order.cancel')}}" enctype="multipart/form-data" style="display: none;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{$item->id}}">
                                <input type="hidden" name="reason" value="">
                                <select class="form-select mt-2" id="cancelReason{{$item->id}}" onchange="updateReason({{$item->id}})">
                                    <option value="ไม่ชำระเงินในเวลาที่กำหนด">ไม่ชำระเงินในเวลาที่กำหนด</option>
                                    <option value="การชำระเงินล้มเหลว">การชำระเงินล้มเหลว</option>
                                    <option value="หลอกลวง">หลอกลวง</option>
                                    <option value="อื่นๆ : ">อื่นๆ</option>
                                </select>
                                <textarea type="text" class="form-control mt-2" id="ectReason{{$item->id}}" style="display: none;" placeholder="กรุณากรอกเหตุผล" onchange="updateReason({{$item->id}})"></textarea>
                                <button class="btn btn-danger btn-sm mt-2" type="submit" onclick="return confirm('ยืนยันว่าจะยกเลิกการสั่งซื้อนี้')">ยืนยัน</button>
                            </form>
                        </div>
                        <div class="col-auto d-none d-lg-block">
                            <a class="underline-none" href="{{route('order', [$item->id])}}">
                                @if ($item->receipt)
                                    {{-- with image --}}
                                    <img 
                                        width="200" height="100%"
                                        src="{{ Storage::url($item->receipt) }}" 
                                        alt="Product Thumbnail" 
                                        class="card-img-top"
                                        style="width: 200px; height: 100%; object-fit: cover;"
                                    >
                                @else
                                    {{-- no image --}}
                                    <svg class="bd-placeholder-img" width="200" height="100%" xmlns="http://www.w3.org/2000/svg" role="img"
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
                <div class="grid-item col-md-6">
                    ไม่มีออร์เดอร์ที่ต้องตรวจ
                </div>
            @endif
        </div>
        {{ $submitted->links() }}

        <!-- Filters -->
        <div class="row mt-5">
            <div class="d-flex justify-content-between">
                <div class="align-self-center">
                    <h1 id="filterLabel">ทั้งหมด</h1>
                </div>
    
                <div class="align-self-center">
                    <form>
                        <select class="form-select" id="orderFilter" name="orderFilter" onchange="changeOrderFilter()">
                            <option value="all">ทั้งหมด</option>
                            <option value="pending">รอโอนเงิน</option>
                            <option value="success">สำเร็จ</option>
                            <option value="cancelled">ยกเลิกแล้ว</option>
                        </select>
                    </form>
                </div>
            </div>
    
            <div class="table-responsive">
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $item)
                        <tr class="order-filter-row" data-status="{{ $item->status }}">
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
        </div>
    @else
        <div class="text text-center">ยังไม่มีคำสั่งซื้อใดๆ</div>
    @endif
@endsection
