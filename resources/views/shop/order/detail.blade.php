@extends('layouts.shop')

@section('title')
คำสั่งซื้อ {{ $order->id }}
@endsection

@section('style')
    .no-underline {
    text-decoration: none;
    }

    .qrcodepay {
        width: 100%; /* Adjust width as needed */
        max-width: 400px; /* Set a max width */
        margin: 0 auto; /* Center horizontally */
    }
@endsection

@section('head-script')
    <script>
        function showCancelForm() {
                document.getElementById("cancel").style.display = "none";
                document.getElementById("cancelForm").style.display = "block";
        }

        function updateReason() {
            const cancelForm = document.getElementById("cancelForm");
            const cancelReason = document.getElementById("cancelReason");
            const ectReason = document.getElementById("ectReason");
            const bankAddress = document.getElementById("bankAddress");
            const bankAddressNameTH = document.getElementById("bankAddressNameTH");
            const bankAddressNameEN = document.getElementById("bankAddressNameEN");
            const bank = document.getElementById("bank");
            const etcBank = document.getElementById("etcBank");

            // Toggle additional reason field
            ectReason.style.display = (cancelReason.value === "อื่นๆ : ") ? "block" : "none";

            // Handle bank refund case
            if (bankAddress) {
                // Toggle other bank field
                etcBank.style.display = (bank.value === "ธนาคารอื่นๆ : ") ? "block" : "none";

                // Format refund message
                const refundDetails = [
                    "เหตุผลที่ยกเลิกคือ",
                    cancelReason.value + ectReason.value,
                    "\nโดยคืนเงินไปที่",
                    bank.value + etcBank.value,
                    "ชื่อบัญชีธนาคาร : " + bankAddressNameTH.value + " (" + bankAddressNameEN.value + ")",
                    "เลขบัญชีธนาคาร : " + bankAddress.value
                ].join("\n");

                cancelForm.reason.value = refundDetails;
            } else {
                // Format cancellation message
                cancelForm.reason.value = [
                    "เหตุผลที่ยกเลิกคือ",
                    cancelReason.value + ectReason.value
                ].join("\n");
            }
        }
    </script>
    
    <script>
        function togglePaymentForm() {
            let selectedMethod = document.getElementById("paymentMethod").value;

            // Hide all forms
            document.querySelectorAll('.payment-form').forEach(form => {
                form.style.display = 'none';
            });

            // Show the selected form
            if (selectedMethod === "bank") {
                document.getElementById("bankForm").style.display = "block";
            } else if (selectedMethod === "qr") {
                document.getElementById("qrForm").style.display = "block";
            }
        }
    </script>

    <script>
        function copyToClipboard() {
            const text = document.getElementById("account-number").innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert("คัดลอกเลขบัญชี : " + text + " เรียบร้อย");
            }).catch(err => {
                console.error("คัดลอกไม่สำเร็จ : ", err);
            });
        }
    </script>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById("preview");

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
        document.addEventListener("DOMContentLoaded", function () {
            let orderStatus = "{{ $order->status }}";
                        
            if(orderStatus === "pending"){
                document.getElementById("payments").style.display = "block";
            }
        });

        function paymentToggle() {
            let x = document.getElementById("details");
            let y = document.getElementById("payments");
            let toggler = document.getElementById("paymentToggler");
            let submit = document.getElementById("submit");
        
            if (x.style.display === "none") {
                x.style.display = "block";
                y.style.display = "none";
                toggler.innerText = "ส่งสลิปใหม่ / ยกเลิกและคืนเงิน";
            } else {
                x.style.display = "none";
                y.style.display = "block";
                toggler.innerText = "กลับ";
                submit.innerText = "อัปโหลดสลิปใหม่";
            }
        }
    </script>

    

@endsection

@section('content')
    <div class="row my-0">
        {{-- order detail --}}     
        <div class="col-md-5 col-lg-4 order-md-last">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-dark">รายการสินค้าที่สั่ง</span>
                <span class="badge bg-dark rounded-pill">{{ $orderItems ? count($orderItems) : 0 }}</span>
            </h4>
            <div class="list-group my-3">
                @foreach($orderItems as $id => $item)
                    <div class="list-group-item list-group-item-primary d-flex align-items-center lh-sm">
                        <div class="w-100">
                            <a href="{{ route('product', $item->product_id) }}" class="d-flex justify-content-between align-items-center w-100 no-underline">
                                <div class="d-flex align-items-center p-2">
                                    <div>
                                        <h6 class="my-1">{{ Str::limit($item->product_name, 24) }}</h6>
                                    </div>
                                </div>
                                @php
                                    $product_optionCount = 0;
                                    foreach ($orderItems as $i => $a) {
                                        if($a->product_id == $item->product_id) {
                                            $product_optionCount++;
                                        }
                                    }
                                @endphp
                                <span class="badge bg-primary rounded-pill">{{$product_optionCount}}</span>
                            </a>
                            <div class="my-2 w-100">
                                <ul class="list-group">
                                    @foreach ($orderItems as $i => $a)
                                        @if($a->product_id == $item->product_id)
                                            <a href="{{ route('product', ['id' => $a->product_id, 'option_id' => $a->product_options_id]) }}" class="animU list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                <div class="d-flex justify-content-between align-items-center gap-3">
                                                    <img class="rounded me-1 img-thumbnail" width="128" height="auto" src="{{ asset($a->option_thumbnail) }}" >
                                                    <div>
                                                        <div class="">{{ $a->option_name }}</div>
                                                        <div class="text-success">฿{{ $a->option_price }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
                <li class="list-group-item list-group-item-primary">
                    <div class="my-1 p-2 d-flex justify-content-between">
                        <span>รวม (THB)</span>
                        <strong class="text-success">฿{{$order->price}}</strong>
                    </div>
                </li>
            </div>

            {{-- redeem code --}}
            {{-- <form class="card p-2">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Promo code">
                    <button type="submit" class="btn btn-secondary">Redeem</button>
                </div>
            </form> --}}
        </div>

        {{-- detail & payments --}}
        <div class="col-md-7 col-lg-8">
            @switch($order->status)
                @case("pending")
                    <h1>การชำระค่าสินค้า</h1>
                    @break
                @case("submitted")
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-info-emphasis" style="margin-bottom: 0;">สถานะ</p>
                            <h1 class="text-info" style="margin-top: 0;">รอตรวจสอบบิล</h1>
                        </div>
                        <div class="text-end">
                            <div class="mb-1 text-body-secondary">
                                {{ convertTimezone($order->created_at) }}
                                <small class="text-body-secondary">({{ calculateDuration($item->updated_at, now()) }}ที่แล้ว)</small>
                            </div>
                            
                            <button class="btn btn-sm btn-info" id="paymentToggler" onclick="paymentToggle()">ส่งสลิปใหม่ / ยกเลิกและคืนเงิน</button>
                        </div>
                    </div>

                    <div id="details" class="my-2">
                        <div class="row">
                            <div class="col-12 col-md-6 p-4">
                                <h4>สลิปที่อัปโหลดไว้</h4>
                                <img class="img-fluid" 
                                src="{{ asset('storage/' . $order->receipt) }}" 
                                alt="Receipt"
                                style="height: auto; width: 100%;">
                            </div>
                        </div>
                    </div>
                    @break
                @case("success")
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-success-emphasis" style="margin-bottom: 0;">สถานะ</p>
                            <h1 class="text-success" style="margin-top: 0;">ส่งสินค้าเรียบร้อย</h1>
                        </div>
                        <div class="text-end">
                            <div class="mb-1 text-body-secondary">{{ convertTimezone($order->created_at) }}</div>
                            <small class="text-body-secondary">{{ calculateDuration($item->updated_at, now()) }}ที่แล้ว</small>
                        </div>
                    </div>

                    <div class="row my-2">
                        <div class="col-12 col-md-6 p-4">
                            <h4>สลิปที่อัปโหลดไว้</h4>
                            <img class="img-fluid" 
                            src="{{ asset('storage/' . $order->receipt) }}" 
                            alt="Receipt"
                            style="height: auto; width: 100%;">
                        </div>
                    </div>
                    @break
                @case("cancelled")
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-danger-emphasis" style="margin-bottom: 0;">สถานะ</p>
                            <h1 class="text-danger" style="margin-top: 0;">ยกเลิกคำสั่งซื้อแล้ว</h1>
                        </div>
                        <div class="text-end">
                            <div class="mb-1 text-body-secondary">{{ convertTimezone($order->created_at) }}</div>
                            <small class="text-body-secondary">{{ calculateDuration($item->updated_at, now()) }}ที่แล้ว</small>
                        </div>
                    </div>

                    <hr>
                        <p>{!!nl2br($cancelDetail->reason)!!}</p>
                    @break
                @case("canceling")
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-danger-emphasis" style="margin-bottom: 0;">สถานะ</p>
                            <h1 class="text-warning" style="margin-top: 0;">กำลังตรวจสอบเพื่อคืนเงินและยกเลิก...</h1>
                        </div>
                        <div class="text-end">
                            <div class="mb-1 text-body-secondary">{{ convertTimezone($order->created_at) }}</div>
                            <small class="text-body-secondary">{{ calculateDuration($item->updated_at, now()) }}ที่แล้ว</small>
                        </div>
                    </div>

                    <hr>
                        <p>{!!nl2br($cancelDetail->reason)!!}</p>
                    <hr>

                    <div class="row my-2">
                        <div class="col-12 col-md-6 p-4">
                            <h4>สลิปที่อัปโหลดไว้</h4>
                            <img class="img-fluid" 
                            src="{{ asset('storage/' . $order->receipt) }}" 
                            alt="Receipt"
                            style="height: auto; width: 100%;">
                        </div>
                    </div>
                    @break
                @case("refund")
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <p class="text-danger-emphasis" style="margin-bottom: 0;">สถานะ</p>
                            <h1 class="text-success" style="margin-top: 0;">คืนเงินสำเร็จ</h1>
                        </div>
                        <div class="text-end">
                            <div class="mb-1 text-body-secondary">{{ convertTimezone($order->created_at) }}</div>
                            <small class="text-body-secondary">{{ calculateDuration($item->updated_at, now()) }}ที่แล้ว</small>
                        </div>
                    </div>

                    <hr>
                        <p>{!!nl2br($cancelDetail->reason)!!}</p>
                    <hr>

                    <div class="row my-2">
                        <div class="col-12 col-md-6 p-4">
                            <h4>สลิปที่อัปโหลดไว้</h4>
                            <img class="img-fluid" 
                            src="{{ asset('storage/' . $order->receipt) }}" 
                            alt="Receipt"
                            style="height: auto; width: 100%;">
                        </div>
                        <div class="col-12 col-md-6 p-4">
                            <h4>หลักฐานการคืนเงิน</h4>
                            <img class="img-fluid" 
                            src="{{ asset('storage/' . $cancelDetail->refundslip) }}" 
                            alt="Refund">
                        </div>
                    </div>
                    @break
                @case("not-refund")
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-danger-emphasis" style="margin-bottom: 0;">สถานะ</p>
                            <h1 class="text-danger" style="margin-top: 0;">ยกเลิกคำสั่งซื้อและไม่ขอคืนเงิน</h1>
                        </div>
                        <div class="text-end">
                            <div class="mb-1 text-body-secondary">{{ convertTimezone($order->created_at) }}</div>
                            <small class="text-body-secondary">{{ calculateDuration($item->updated_at, now()) }}ที่แล้ว</small>
                        </div>
                    </div>

                    <hr>
                        <p>{!!nl2br($cancelDetail->reason)!!}</p>
                    <hr>

                    <div class="row my-2">
                        <div class="col-12 col-md-6 p-4">
                            <h4>สลิปที่อัปโหลดไว้</h4>
                            <img class="img-fluid" 
                            src="{{ asset('storage/' . $order->receipt) }}" 
                            alt="Receipt"
                            style="height: auto; width: 100%;">
                        </div>
                    </div>
                    @break
                @default
                    <div>
                        <p class="text-danger-emphasis" style="margin-bottom: 0;">สถานะ</p>
                        <h1 class="text-danger" style="margin-top: 0;">มีปัญหาที่ระบบ โปรดแจ้งเจ้าหน้าที่</h1>
                    </div>

                    <div class="my-2">
                        <p style="margin-bottom: 0;">สลิปที่อัปโหลดไว้</p>
                        <img style="margin-top: 0;" class="img-fluid" src="{{ asset('storage/' . $order->receipt) }}" alt="Receipt Image">
                    </div>
                    @break
            @endswitch

            <div id="payments" style="display: none;">
                <div class="row my-4">
                    @if ($order->status == "pending")
                        <!-- Payment Selector -->
                        <form class="my-2">
                            <label for="paymentMethod" class="form-label">เลือกวิธีชำระเงิน</label>
                            <select class="form-select" id="paymentMethod" name="paymentMethod" onchange="togglePaymentForm()">
                                <option value="qr">QR Promptpay</option>
                                <option value="bank">โอนผ่านธนาคาร</option>
                            </select>
                        </form>
                        <!-- qr -->
                        <div id="qrForm" class="payment-form">
                            <div class="qrcodepay col-lg-12 my-2 text-center rounded">
                                <div class="rounded" style="
                                            background-color: #0e3d67;
                                            height: 54px;
                                            text-align: center;
                                        ">
                                        <img src="{{url('images/payment/ic_prompt_pay_banner@3x.png')}}"  style="
                                            max-height: 100%; 
                                            max-width: 100%; 
                                            object-fit: contain;">
                                </div>
                                <img class="mt-4" src="{{url('images/payment/krupatQR.png')}}"  style="
                                            max-height: 100%; 
                                            max-width: 60%; 
                                            object-fit: contain;">
                                <p class="mt-4"><strong>ห้องสื่อสวย by ครูพัช</strong></p>
                                <p><small><strong>Kru Pat Education Room</strong></small></p>
                                {{-- <hr class="border-2 opacity-50" style="border-color: #0e3d67">
                                <p class="mt-4">ชื่อบัญชี : สันต์ชัช อินต๊ะ</p>
                                <p>รหัสร้านค้า : 014000006187660</p> --}}
                                <hr class="border-2 opacity-50" style="border-color: #0e3d67">
                                <p>Accept all banks | รับเงินได้จากทุกธนาคาร</p>
                                <p class="text-danger">โปรดตรวจสอบยอดเงินทุกครั้งก่อนโอน ทางบริษัทไม่มีนโยบายคืนเงินทุกกรณี</p>
                                <hr class="border-5 opacity-100" style="border-color: #0e3d67">
                            </div>
                        </div>

                        <!-- bank -->
                        <div id="bankForm" class="payment-form" style="display: none;">
                            <div class="qrcodepay col-lg-12 my-2 text-center rounded">
                                <div class="rounded" style="
                                            background-color: #4e2a82;
                                            height: 54px;
                                            text-align: center;
                                        ">
                                        <img src="{{url('images/payment/scbLogo.jpg')}}"  style="
                                            max-height: 100%; 
                                            max-width: 100%; 
                                            object-fit: contain;">
                                </div>

                                <h1 class="my-3 d-flex justify-content-center align-items-start" style="gap: 8px;">
                                    <strong id="account-number">519-407217-1</strong>
                                    <button onclick="copyToClipboard()" class="btn p-0 btn-sm">
                                        <img src="{{ asset('icons/clipboard-2-svgrepo-com.svg') }}" alt="Clipboard Icon" width="16">
                                    </button>
                                </h1>
                                <p><strong>ชื่อบัญชี : สันต์ชัช อินต๊ะ</strong></p>
                                <hr class="border-2 opacity-50" style="border-color: #4e2a82">
                                <p>กรุณากรองจำนวนเงินให้ตรงกับยอดรวมด้วยนะคะ</p>
                                <p>เมื่อชำระเงินเรียบร้อยแล้วอัพโหลดสลิปโอนเงินได้เลยค่ะ</p>
                                <p class="text-danger">โปรดตรวจสอบยอดเงินทุกครั้งก่อนโอน ทางบริษัทไม่มีนโยบายคืนเงินทุกกรณี</p>
                                <hr class="border-5 opacity-100" style="border-color: #4e2a82">
                            </div>                    
                        </div>
                    @endif

                    <div class="col-lg-12 my-4 align-self-center">
                        <form method="POST" action="checkout" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$order->id}}">

                            <label for="receipt" class="form-label">อัปโหลดสลิป</label>
                            <input type="file" class="form-control" name="receipt" id="receipt" accept="image/*" onchange="previewImage(event)">
                            @error('receipt')
                                <div class="ms-2 p-2" id="error-section">
                                    <span class="text text-danger">{{ $message }}</span>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        document.getElementById('error-section').scrollIntoView();
                                    });
                                </script>
                            @enderror

                            <div class="my-2 text-center">
                                <img id="preview" src="#" alt="Preview Image" style="max-width: 100%; height: auto; display: none; border-radius: 8px; border: 1px solid #ddd;">
                            </div>

                            <button id="submit" class="w-100 btn btn-success btn-lg my-2" type="submit">ยืนยันการชำระเงิน</button>
                        </form>
                        <hr>
                        @if($order->status == 'pending')
                            <form id="cancelForm" method="POST" action="{{route('order.cancel')}}" enctype="multipart/form-data" style="display: none;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{$order->id}}">
                                <input type="hidden" name="reason" value="">
                                <label for="cancelReason" class="form-label">เลือกเหตุผลในการยกเลิก</label>
                                <select class="form-select mb-2" id="cancelReason" onchange="updateReason()">
                                    <option value="ไม่ต้องการสินค้า/บริการดังกล่าวแล้ว">ไม่ต้องการสินค้า/บริการดังกล่าวแล้ว</option>
                                    <option value="ต้องการเลือกซื้อสินค้าเพิ่มเติม">ต้องการเลือกซื้อสินค้าเพิ่มเติม</option>
                                    <option value="อื่นๆ : ">อื่นๆ</option>
                                </select>
                                <textarea type="text" class="form-control" id="ectReason" placeholder="เหตุผล" onchange="updateReason()"></textarea>                                
                                <button class="w-100 btn btn-danger btn-lg my-2" type="submit" onclick="return confirm('ยืนยันว่าจะยกเลิกการสั่งซื้อนี้ เมื่อกดยืนยันแล้วจะไม่สามารถถอนคำร้องได้นะคะ?!')">ยืนยัน</button>
                            </form>
                            <button id="cancel" class="w-100 btn btn-danger btn-lg my-2" style="display: block" onclick="showCancelForm(); updateReason(); return false;">ยกเลิกคำสั่งซื้อ</button>
                        @else
                            <form id="cancelForm" method="POST" action="{{route('order.cancel')}}" enctype="multipart/form-data" style="display: none;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{$order->id}}">
                                <input type="hidden" name="reason" value="">
                                <label for="cancelReason" class="form-label">เลือกเหตุผลในการยกเลิก</label>
                                <select class="form-select mb-2" id="cancelReason" onchange="updateReason()">
                                    <option value="ไม่ต้องการสินค้า/บริการดังกล่าวแล้ว">ไม่ต้องการสินค้า/บริการดังกล่าวแล้ว</option>
                                    <option value="ต้องการเลือกซื้อสินค้าเพิ่มเติม">ต้องการเลือกซื้อสินค้าเพิ่มเติม</option>
                                    <option value="อื่นๆ : ">อื่นๆ</option>
                                </select>
                                <textarea type="text" class="form-control" id="ectReason" placeholder="เหตุผล" onchange="updateReason()"></textarea>

                                <label for="bank" class="form-label mt-2">ธนาคาร</label>
                                <select class="form-select mb-2" id="bank" onchange="updateReason()">
                                    <option value="ธนาคารกรุงเทพ จำกัด (มหาชน)">ธนาคารกรุงเทพ จำกัด (มหาชน)</option>
                                    <option value="ธนาคารกรุงไทย จำกัด (มหาชน)">ธนาคารกรุงไทย จำกัด (มหาชน)</option>
                                    <option value="ธนาคารกรุงศรีอยุธยา จำกัด (มหาชน)">ธนาคารกรุงศรีอยุธยา จำกัด (มหาชน)</option>
                                    <option value="ธนาคารกสิกรไทย จำกัด (มหาชน)">ธนาคารกสิกรไทย จำกัด (มหาชน)</option>
                                    <option value="ธนาคารเกียรตินาคินภัทร จำกัด (มหาชน)">ธนาคารเกียรตินาคินภัทร จำกัด (มหาชน)</option>
                                    <option value="ธนาคารซีไอเอ็มบี ไทย จำกัด (มหาชน)">ธนาคารซีไอเอ็มบี ไทย จำกัด (มหาชน)</option>
                                    <option value="ธนาคารทหารไทยธนชาต จำกัด (มหาชน)">ธนาคารทหารไทยธนชาต จำกัด (มหาชน)</option>
                                    <option value="ธนาคารทิสโก้ จำกัด (มหาชน)">ธนาคารทิสโก้ จำกัด (มหาชน)</option>
                                    <option value="ธนาคารไทยเครดิต จำกัด (มหาชน)">ธนาคารไทยเครดิต จำกัด (มหาชน)</option>
                                    <option value="ธนาคารไทยพาณิชย์ จำกัด (มหาชน)">ธนาคารไทยพาณิชย์ จำกัด (มหาชน)</option>
                                    <option value="ธนาคารยูโอบี จำกัด (มหาชน)">ธนาคารยูโอบี จำกัด (มหาชน)</option>
                                    <option value="ธนาคารแลนด์ แอนด์ เฮ้าส์ จำกัด (มหาชน)">ธนาคารแลนด์ แอนด์ เฮ้าส์ จำกัด (มหาชน)</option>
                                    <option value="ธนาคารสแตนดาร์ดชาร์เตอร์ด (ไทย) จำกัด (มหาชน)">ธนาคารสแตนดาร์ดชาร์เตอร์ด (ไทย) จำกัด (มหาชน)</option>
                                    <option value="ธนาคารไอซีบีซี (ไทย) จำกัด (มหาชน)">ธนาคารไอซีบีซี (ไทย) จำกัด (มหาชน)</option>
                                    <option value="ธนาคารแห่งประเทศจีน (ไทย) จำกัด (มหาชน)">ธนาคารแห่งประเทศจีน (ไทย) จำกัด (มหาชน)</option>
                                    <option value="ธนาคารอื่นๆ : ">อื่นๆ</option>
                                </select>
                                <input type="text" class="form-control" id="etcBank" placeholder="ชื่อธนาคาร" onchange="updateReason()"></input>

                                <label for="bankAddress" class="form-label mt-2">บัญชีธนาคารที่ต้องการให้คืนเงินกลับ</label>
                                <input type="text" class="form-control mb-1" id="bankAddressNameTH" placeholder="ชื่อบัญชีธนาคารภาษาไทย" onchange="updateReason()"></input>
                                <input type="text" class="form-control mb-1" id="bankAddressNameEN" placeholder="ชื่อบัญชีธนาคารภาษาอังกฤษ" onchange="updateReason()"></input>
                                <input type="text" class="form-control mb-1" id="bankAddress" placeholder="เลขบัญชีธนาคาร" onchange="updateReason()"></input>
                                <small class="text-danger">*ได้โปรดกรอกให้ครบทุกช่องนะคะ จะได้คืนเงินได้ถูกต้องสมบูรณ์</small>
                                
                                <button class="w-100 btn btn-danger btn-lg my-2" type="submit" onclick="return confirm('ยืนยันว่าจะยกเลิกการสั่งซื้อนี้ เมื่อกดยืนยันแล้วจะไม่สามารถถอนคำร้องได้นะคะ?!')">ยืนยัน</button>
                            </form>
                            <button id="cancel" class="w-100 btn btn-danger btn-lg my-2" style="display: block" onclick="showCancelForm(); updateReason(); return false;">ยกเลิกคำสั่งซื้อและขอคืนเงิน</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection