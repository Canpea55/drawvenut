<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Http\Middleware\IsAdmin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderCancelation;
use Hamcrest\Number\OrderingComparison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function orders() {
        if(auth()){
            $orders = Order::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
            $orderItems = collect();
            foreach ($orders as $order) {
                $items = OrderItem::where('order_id', $order->id)->get();
                $orderItems = $orderItems->merge($items);
            }
            return view('shop.order.orders', compact('orders', 'orderItems'));
        }
    }

    public function order($order_id) {
        $order = Order::findOrFail($order_id);
        if($order->user_id == Auth::user()->id || Auth::user()->isAdmin){
            $orderItems = OrderItem::where('order_id', $order_id)->get();
            $cancelDetail = OrderCancelation::where('order_id', $order_id)?->first();
            return view('shop.order.detail', compact(['order', 'orderItems', 'cancelDetail']));
        } else {
            abort(403, 'Unauthorized');
        }
    }

    public function add(Request $request) {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $request->total,
                'status' => "pending",
            ]);

            if($order->total <= 0) {
                $items = json_decode($request->items);
                $orderItems = [];
                foreach($items as $id => $item) {
                    foreach($item->selectedOptions as $option_id) {
                        foreach($item->optionDetails as $detail) {
                            if($option_id == $detail->option_id){
                                $orderItems[] = OrderItem::create([
                                    'order_id' => $order->id,
                                    
                                    'product_id' => $id,
                                    'product_name' => $item->name,
            
                                    'product_options_id' => $detail->option_id,
                                    'option_thumbnail' => $detail->thumbnail,
                                    'option_name' => $detail->name,
                                    'option_price' => $detail->price,
                                ]);
                            }
                        }
                    }
                };

                app(CartController::class)->clear();

                $libraryController = new LibraryController();
                foreach ($orderItems as $item) {
                    $libraryController->add($order->user_id, $item->product_options_id);
                }

                $order->status = 'success';
                $order->save();

                DB::commit();
                return redirect(route('library',  ['product_id' => $request->product_id]))->with([
                    'success' => 'ได้รับสินค้าฟรีแล้วค่ะ!',
                    'toast-icon' => asset('icons/oval-interface/gift-present.svg'),
                ]);
            }
            
            $items = json_decode($request->items);
            foreach($items as $id => $item) {
                foreach($item->selectedOptions as $option_id) {
                    foreach($item->optionDetails as $detail) {
                        if($option_id == $detail->option_id){
                            $orderitem = OrderItem::create([
                                'order_id' => $order->id,
                                
                                'product_id' => $id,
                                'product_name' => $item->name,
        
                                'product_options_id' => $detail->option_id,
                                'option_thumbnail' => $detail->thumbnail,
                                'option_name' => $detail->name,
                                'option_price' => $detail->price,
                            ]);
                        }
                    }
                }
            };
    
            app(CartController::class)->clear();
            
            DB::commit();
            return redirect(route('order',  $order->id))->with('success', 'สั่งซื้อเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'error' => "พบ error : \n\n" . $e->getMessage(),
                'toast-icon' => asset('icons/oval-interface/circle-emphasis.svg'),
            ]);
        }
    }

    public function checkout(Request $request) {
        $id = $request->input("id");

        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096', // Validate image file
        ],
        [
            'receipt.required'=>'กรุณาเลือกไฟล์สลิปก่อนนะคะ',
            'receipt.image'=>'ไฟล์ต้องเป็นภาพนะคะ ( jpeg, png, jpg, gif )',
            'receipt.max'=>'ไฟล์ใหญ่เกินไปนะคะ (ไม่เกิน 4 MB)',
            'receipt.mimes'=>'ไฟล์แบบนี้หนูไม่รู้จักค่ะ (jpeg, png, jpg, gif)',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt')) {
            $image = $request->file('receipt');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('receipts'), $filename);
            $imagePath = 'receipts/' . $filename;

            // Update order with image path
            Order::find($id)->update([
                'status' => "submitted",
                'receipt' => $imagePath, // Store only the path in the database
            ]);
        }

        return redirect()->back()->with([
            'success' => 'ส่งสลิปเรียบร้อยแล้ว! รอการอนุมัติจากผู้ดูแลระบบนะคะ',
            'toast-icon' => asset('icons/oval-interface/done-v.svg'),
        ]);
    }

    public function cancel(Request $request) {
        $id = $request->order_id;
        $order = Order::findOrFail($id);
        $canceller = auth()->id();
        $reason = $request->reason;
        $cancel_status = $request->status;
        if ($request->hasFile('refundslip')) {
            $image = $request->file('refundslip');
            $imagePath = $image->store('refundslip', 'public'); // Save to storage/app/public/receipts
        }
        $exist = OrderCancelation::where('order_id', $id)->get();
        DB::beginTransaction();
        if($exist->isNotEmpty()){
            switch($cancel_status){
                case "refund": 
                    try {
                        $cancel_data = [
                            'reason' => $reason,
                            'status' => 'approved',
                            'refundslip' => $imagePath,
                        ];
                        OrderCancelation::find($exist->first()->id)?->update($cancel_data);
                        $order->status = "refund";
                        $order->save();
                        DB::commit();
                        return redirect()->back()->with([
                            'success' => 'การสั่งซื้อถูกยกเลิกเรียบร้อยแล้ว! และคืนเงินเรียบร้อยแล้ว!',
                            'toast-icon' => asset('icons/oval-interface/done-v.svg'),
                        ]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return redirect()->back()->with([
                            'error' => "พบ error : \n\n" . $e->getMessage(),
                            'toast-icon' => asset('icons/oval-interface/circle-emphasis.svg'),
                        ]);
                    }
                case "not-refund": 
                    try {
                        $cancel_data = [
                            'reason' => $reason,
                            'status' => 'approved',
                        ];
                        OrderCancelation::find($exist->first()->id)?->update($cancel_data);
                        $order->status = "not-refund";
                        $order->save();
                        DB::commit();
                        return redirect()->back()->with([
                            'success' => 'การสั่งซื้อถูกยกเลิกเรียบร้อยแล้ว! และไม่คืนเงิน!',
                            'toast-icon' => asset('icons/oval-interface/done-v.svg'),
                        ]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return redirect()->back()->with([
                            'error' => "พบ error : \n\n" . $e->getMessage(),
                            'toast-icon' => asset('icons/oval-interface/circle-emphasis.svg'),
                        ]);
                    }
                default:
                    break;
            }
        } else {
            if($order->status == "pending" || Auth::user()->isAdmin){
                try {
                    $cancelation = OrderCancelation::create([
                        'order_id' => $id,
                        'user_id' => $canceller,
                        'reason' => $reason,
                        'status' => 'approved',
                    ]);
                    $order->status = "cancelled";
                    $order->save();
                    DB::commit();
                    return redirect()->back()->with([
                        'success' => 'การสั่งซื้อถูกยกเลิกเรียบร้อยแล้ว!',
                        'toast-icon' => asset('icons/oval-interface/done-v.svg'),
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->back()->with([
                        'error' => "พบ error : \n\n" . $e->getMessage(),
                        'toast-icon' => asset('icons/oval-interface/circle-emphasis.svg'),
                    ]);
                }
            } else {
                try {
                    OrderCancelation::create([
                        'order_id' => $id,
                        'user_id' => $canceller,
                        'reason' => $reason,
                        'status' => 'pending',
                    ]);
                    $order->status = "canceling";
                    $order->save();
                    DB::commit();
                    return redirect()->back()->with([
                        'pending' => 'ส่งคำขอยกเลิกการสั่งซื้อแล้ว! รอการอนุมัติจากผู้ดูแลระบบนะคะ',
                        'toast-icon' => asset('icons/oval-interface/done-v.svg'),
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->back()->with([
                        'error' => "พบ error : \n\n" . $e->getMessage(),
                        'toast-icon' => asset('icons/oval-interface/circle-emphasis.svg'),
                    ]);
                }
            }
        }
    }

    //--------------------------------------------admin--------------------------------------------
    public function manage(Request $request) {
        $orders=Order::orderBy('id', 'desc')->paginate(10, ['*'], 'orders');
        $submitted=Order::where('status', 'submitted')->orderBy('created_at', 'asc')->paginate(4, ['*'], 'submitted');
        $canceling=Order::where('status', 'canceling')->orderBy('created_at', 'asc')->paginate(4, ['*'], 'canceling');

        $cancelDetail = OrderCancelation::orderBy('id', 'desc')->get();

        return view('admin.order.manage', compact(['orders', 'submitted', 'canceling','cancelDetail']));
    }

    public function approve(Request $request) {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($request->id);
            $orderItems = OrderItem::where('order_id', $request->id)->get();

            $libraryController = new LibraryController(); // Create instance

            foreach ($orderItems as $item) {
                $libraryController->add($order->user_id, $item->product_options_id);
            }

            $order->status = 'success';
            $order->save();

            DB::commit();
            return redirect()->back()->with([
                'success' => "อนุมัติการสั่งซื้อเรียบร้อย! ส่งสินค้าไปยังคลังของลูกค้าแล้ว! \n\n" . "หมายเลขการสั่งซื้อ : " . $request->id . " \n" . "ยอดรวม : " . priceTag($order->total), 
                'toast-icon' => asset('icons/oval-interface/done-v.svg'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'error' => "พบ error : \n\n" . $e->getMessage(),
                'toast-icon' => asset('icons/oval-interface/circle-emphasis.svg'),
            ]);
        }
    }

    public function remove(Request $request) {
    }
}
