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
            document.getElementById("datatable").scrollIntoView();

            document.cookie = `orderFilter=${selectedFilter}; path=/; max-age=${60}`;

            document.querySelectorAll('.order-filter-row').forEach(row => {
                row.style.display = 'none';
            });

            if(selectedFilter == 'all'){
                document.querySelectorAll('.order-filter-row').forEach(row => {
                    row.style.display = 'table-row';
                });
            } else {
                document.querySelectorAll(`.order-filter-row[data-status="${selectedFilter}"]`).forEach(row => {
                    row.style.display = 'table-row';
                });
            }

            let filterLabel = {
                'all': 'ทั้งหมด',
                'pending': 'รอโอนเงิน',
                'success': 'เรียบร้อย',
                'canceling': 'ขอยกเลิก',
                'cancelled': 'ยกเลิกแล้ว',
                'refund': 'คืนเงินแล้ว',
                'not-refund': 'ไม่คืนเงิน',
            };

            document.getElementById("filterLabel").innerText = filterLabel[selectedFilter] || 'ทั้งหมด';
        }

        function showCancelForm(order_id) {
            let cancelform = document.getElementById("cancelForm" + order_id);
            switch(document.getElementById("cancelForm" + order_id).style.display){
                case 'none' : 
                cancelform.style.display = "block";
                break;
                case ' block' :
                cancelform.style.display = "none";
                break;
            }
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
    <script type="module">
        var $grid = $('.grid').masonry({
                itemSelector: '.grid-item',
        });
        // Layout Masonry after each image loads
        $('.grid img').each(function() {
                var $img = $(this);
                imagesLoaded($img[0], function() {
                    $grid.masonry('layout');
                });
        });
    </script>

    <script>
        function previewImage(event, order_id) {
            const input = event.target;
            const preview = document.getElementById("preview" + order_id);

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <script>
        function confirmCancel(order_id){
            var confirmMessage = '';
            let refundingop = document.getElementById("refunding" + order_id).value;
            switch(refundingop) {
                case "refund" : 
                    confirmMessage = 'ดำเนินการยกเลิกรายการที่ ' + order_id + ' โดยคืนเงินไปยังผู้ใช้แล้ว'
                    break;
                case "notRefund" : 
                    confirmMessage = 'ดำเนินการยกเลิกรายการที่ ' + order_id + ' โดยไม่คืนเงินไปยังผู้ใช้ โดยเหตุผลว่า \n\"' + document.getElementById('notRefund' + order_id).value + "\"";
                    break;
                default: confirmMessage = 'ดำเนินการยกเลิกรายการที่' + order_id + ' โดยคืนเงินไปยังผู้ใช้';
            }
            return confirm(confirmMessage);
        }

        function updateRefund(order_id){
            let refundingop = document.getElementById("refunding" + order_id).value;
            switch(refundingop) {
                case "refund" : 
                    document.getElementById('notRefund' + order_id).style.display = "none";
                    document.getElementById('refundslip' + order_id).style.display = "block";
                    document.getElementById('refundForm' + order_id).reason.value = document.getElementById('refundForm'+order_id).submitter_reason.value;
                    document.getElementById('refundForm' + order_id).status.value = "refund";
                    break;
                case "notRefund" : 
                    document.getElementById('notRefund' + order_id).style.display = "block";
                    document.getElementById('refundslip' + order_id).style.display = "none";
                    document.getElementById('refundForm' + order_id).reason.value = document.getElementById('refundForm'+order_id).submitter_reason.value + "\n\n[เหตุผลที่ไม่คืนเงินจากผู้ตรวจ]\n" + document.getElementById('notRefund' + order_id).value;
                    document.getElementById('refundForm' + order_id).status.value = "not-refund";
                    break;
            }
            updateLayout();
        }
        function updateLayout(){
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
        <div class="grid row row-cols-1 row-cols-md-3 g-3">
            @if ($submitted->isNotEmpty() || $canceling->isNotEmpty())
                @foreach ($submitted as $item)
                <div class="grid-item col-md-4" onmouseover="updateReason({{$item->id}})">
                    <div class="animU row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
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
                            <div class="d-flex justify-content-between align-items-center pt-4">
                                <div class="btn-group w-100">
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
                    </div>
                </div>
                @endforeach
                @foreach ($canceling as $item)
                    @php
                        $reason = '';
                        foreach($cancelDetail as $cDetail){
                            if($cDetail->order_id == $item->id){
                                $reason = $cDetail->reason;
                            }
                        }
                    @endphp
                    <div class="grid-item col-md-4" onmouseover="updateRefund({{$item->id}})">
                        <div class="animU row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                            <div class="col p-4 d-flex flex-column position-static">
                                <a class="no-underline" href="{{route('order', [$item->id])}}">
                                    <strong class="d-inline-block mb-2 text-primary-emphasis">{{$item->id}}</strong>
                                    <h3 class="mb-0">฿{{$item->total}}</h3>
                                </a>
                                    <div class="mb-1 text-body-secondary">{{ convertTimezone($item->created_at)  }}</div>
                                    <small class="text-body-secondary">{{ calculateDuration($item->updated_at, now()) }}ที่แล้ว</small>
                                    <p class="my-2">{!!nl2br($reason)!!}</p>
                                <p class="card-text mb-auto"></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <form id="refundForm{{$item->id}}" method="POST" action="{{route(name: 'order.cancel')}}" enctype="multipart/form-data" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{$item->id}}">
                                        <input type="hidden" name="submitter_reason" value="{{$reason}}">
                                        <input type="hidden" name="reason" value="">
                                        <input type="hidden" name="status" value="">
                                    </form>
                                    <button id="cancelingBtn" 
                                            form="refundForm{{$item->id}}"
                                            class="btn btn-warning btn-lg mt-2 w-100" 
                                            type="" 
                                            onclick="return confirmCancel({{$item->id}});">ดำเนินการยกเลิก
                                    </button>
                                </div>
                                <select class="form-select mt-2" id="refunding{{$item->id}}" onchange="updateRefund({{$item->id}})">
                                    <option value="refund">คืนเงิน</option>
                                    <option value="notRefund">ไม่คืนเงิน</option>
                                </select>

                                <div id="refundslip{{$item->id}}" class="card mt-2">
                                    <input form="refundForm{{$item->id}}" title="สลิปโอนเงินคืน" type="file" class="form-control" name="refundslip" accept="image/*" onchange="previewImage(event, {{$item->id}})">
                                    @error('receipt')
                                        <div class="ms-2 p-2">
                                            <span class="text text-danger">{{ $message }}</span>
                                        </div>
                                    @enderror
                                    <div class="text-center">
                                        <img class="mt-2" id="preview{{$item->id}}" src="#" alt="Preview Image" style="max-width: 100%; height: auto; display: none; border-radius: 8px; border: 1px solid #ddd;">
                                    </div>
                                </div>

                                <textarea type="text" class="form-control mt-2" id="notRefund{{$item->id}}" style="display: none;" placeholder="กรุณากรอกเหตุผล" onchange="updateRefund({{$item->id}})"></textarea>
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
                            <option value="canceling">ขอยกเลิก</option>
                            <option value="cancelled">ยกเลิกแล้ว</option>
                            <option value="refund">คืนเงินแล้ว</option>
                            <option value="not-refund">ไม่คืนเงิน</option>
                        </select>
                    </form>
                </div>
            </div>
    
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered align-middle">
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
