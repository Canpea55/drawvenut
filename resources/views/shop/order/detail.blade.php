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
                toggler.innerText = "อัปโหลดสลิปใหม่ / ยกเลิกคำสั่งซื้อ";
            } else {
                x.style.display = "none";
                y.style.display = "block";
                toggler.innerText = "ยกเลิกอัปโหลดสลิป";
                submit.innerText = "อัปโหลดสลิปใหม่";
            }
        }
    </script>

    <script>
        function showCancelForm() {
                document.getElementById("cancel").style.display = "none";
                document.getElementById("cancelForm").style.display = "block";
        }

        function updateReason() {
            if(document.getElementById("cancelReason").value === "อื่นๆ : ") {
                document.getElementById("ectReason").style.display = "block";
                document.getElementById("cancelForm").reason.value = document.getElementById("cancelReason").value + document.getElementById("ectReason").value;
            } else {
                document.getElementById("ectReason").style.display = "none";
                document.getElementById("cancelForm").reason.value = document.getElementById("cancelReason").value;
            }
        }
    </script>
@endsection

@section('content')
    <div class="row g-3 my-0">
        {{-- order detail --}}     
        <div class="col-md-5 col-lg-4 order-md-last">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">รายการสินค้าที่สั่ง</span>
                <span class="badge bg-primary rounded-pill">{{ $orderItems ? count($orderItems) : 0 }}</span>
            </h4>
            <ul class="list-group my-3">
                @foreach($orderItems as $id => $item)
                    <li class="list-group-item d-flex align-items-center justify-content-between lh-sm">
                        <div class="d-flex align-items-center gap-3">

                            <!-- Image Section -->
                            <a class="no-underline" href="{{ route('product', $item->product_id) }}">
                                @if(!empty($item->thumbnail))
                                    <img src="{{ Storage::url($item->thumbnail) }}" alt="Product Thumbnail"
                                        class="rounded flex-shrink-0 card" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <svg class="bd-placeholder-img rounded flex-shrink-0 card" width="50" height="50"
                                        xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail"
                                        preserveAspectRatio="xMidYMid slice" focusable="false">
                                        <rect width="100%" height="100%" fill="#D09CC7" />
                                    </svg>
                                @endif
                            </a>

                            <!-- Text Section (Takes up remaining space) -->
                            <div class="flex-grow-1">
                                <a class="no-underline" href="{{ route('product', $item->product_id) }}">
                                    <h6 class="my-1">{{ Str::limit($item->name, 24) }}</h6>
                                </a>
                                <span class="text-body-secondary">฿{{ $item->price }}</span>
                            </div>

                        </div>
                    </li>
                @endforeach
                <li class="list-group-item">
                    <div class="my-3 d-flex justify-content-between">
                        <span>รวม (THB)</span>
                        <strong>฿{{$order->total}}</strong>
                    </div>
                </li>
            </ul>

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
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-info-emphasis" style="margin-bottom: 0;">สถานะ</p>
                            <h1 class="text-info" style="margin-top: 0;">รอตรวจสอบบิล</h1>
                        </div>

                        <button class="btn btn-sm btn-info align-self-center" id="paymentToggler" onclick="paymentToggle()">อัปโหลดสลิปใหม่ / ยกเลิกคำสั่งซื้อ</button>
                    </div>

                    <div id="details" class="my-2">
                        <div class="text-center">
                            <p style="margin-bottom: 0;">สลิปที่อัปโหลดไว้</p>
                            <img style="margin-top: 0;" class="img-fluid" src="{{ asset('storage/' . $order->receipt) }}" alt="Receipt Image">
                        </div>
                    </div>
                    @break
                @case("success")
                    <div>
                        <p class="text-success-emphasis" style="margin-bottom: 0;">สถานะ</p>
                        <h1 class="text-success" style="margin-top: 0;">ส่งสินค้าเรียบร้อย</h1>
                    </div>

                    <div class="text-center my-2">
                        <p style="margin-bottom: 0;">สลิปที่อัปโหลดไว้</p>
                        <img style="margin-top: 0;" class="img-fluid" src="{{ asset('storage/' . $order->receipt) }}" alt="Receipt Image">
                    </div>
                    @break
                @case("canceling")
                    <div>
                        <p class="text-danger-emphasis" style="margin-bottom: 0;">สถานะ</p>
                        <h1 class="text-danger" style="margin-top: 0;">กำลังยกเลิก...</h1>
                    </div>

                    <div class="text-center my-2">
                        <p style="margin-bottom: 0;">สลิปที่อัปโหลดไว้</p>
                        <img style="margin-top: 0;" class="img-fluid" src="{{ asset('storage/' . $order->receipt) }}" alt="Receipt Image">
                    </div>
                    @break
                @case("cancelled")
                    <div>
                        <p class="text-danger-emphasis" style="margin-bottom: 0;">สถานะ</p>
                        <h1 class="text-danger" style="margin-top: 0;">ยกเลิกคำสั่งซื้อแล้ว</h1>
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
                                <div class="ms-2 p-2">
                                    <span class="text text-danger">{{ $message }}</span>
                                </div>
                            @enderror

                            <div class="my-2 text-center">
                                <img id="preview" src="#" alt="Preview Image" style="max-width: 100%; height: auto; display: none; border-radius: 8px; border: 1px solid #ddd;">
                            </div>

                            <button id="submit" class="w-100 btn btn-success btn-lg my-2" type="submit">ยืนยันการชำระเงิน</button>
                        </form>
                        <hr>
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
                            <textarea type="text" class="form-control" id="ectReason" style="display: none;" placeholder="กรุณากรอกเหตุผล" onchange="updateReason()"></textarea>
                            
                            <button class="w-100 btn btn-danger btn-lg my-2" type="submit" onclick="return confirm('ยืนยันว่าจะยกเลิกการสั่งซื้อนี้ เมื่อกดยืนยันแล้วจะไม่สามารถถอนคำร้องได้นะคะ?!')">ยืนยัน</button>
                        </form>
                        <button id="cancel" class="w-100 btn btn-danger btn-lg my-2" style="display: block" onclick="showCancelForm(); updateReason(); return false;">ยกเลิกคำสั่งซื้อ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection