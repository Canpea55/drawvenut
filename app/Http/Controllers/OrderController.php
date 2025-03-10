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
    public function orders()
    {
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

    public function order($order_id)
    {
        $order = Order::findOrFail($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->get();
        return view('shop.order.detail', compact('order', 'orderItems'));
    }

    public function add(Request $request)
    {
        $order = Order::create([
            'user_id' => auth()->id(),
            'total' => $request->total,
            'status' => "pending",
        ]);
        
        $products = json_decode($request->products);
        foreach($products as $id => $product){
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'name' => $product->name,
                'thumbnail' => $product->image,
                'price' => $product->price,
            ]);
        };

        app(CartController::class)->clear();
        return redirect(route('order',  $order->id));
    }

    public function checkout(Request $request)
    {
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
            $imagePath = $image->store('images/receipts', 'public'); // Save to storage/app/public/receipts

            // Update order with image path
            Order::find($id)->update([
                'status' => "submitted",
                'receipt' => $imagePath, // Store only the path in the database
            ]);
        }

        return redirect()->back()->with('success', 'Receipt uploaded successfully!');
    }

    public function cancel(Request $request){
        
        $id = $request->order_id;
        $order = Order::findOrFail($id);
        $canceller = auth()->id();
        $reason = $request->reason;
        if($order->status == "pending" || Auth::user()->isAdmin){
            $cancelation = OrderCancelation::create([
                'order_id' => $id,
                'user_id' => $canceller,
                'reason' => $reason,
                'status' => 'approved',
            ]);
            $order->status = "cancelled";
            $order->save();
            return redirect()->back()->with('success', 'Order cancelled successfully!');
        } else {
            OrderCancelation::create([
                'order_id' => $id,
                'user_id' => $canceller,
                'reason' => $reason,
                'status' => 'pending',
            ]);
            $order->status = "canceling";
            $order->save();
            return redirect()->back()->with('pending', 'Order cancelation submited!');
        }
    }

    //--------------------------------------------admin--------------------------------------------
    public function manage(Request $request)
    {
        $orders=Order::orderBy('id', 'desc')->paginate(10, ['*'], 'orders');
        $pending=Order::where('status', 'pending')->orderBy('id', 'desc')->paginate(10, ['*'], 'pending');
        $submitted=Order::where('status', 'submitted')->orderBy('created_at', 'asc')->paginate(4, ['*'], 'submitted');
        $success=Order::where('status', 'success')->orderBy('id', 'desc')->paginate(10, ['*'], 'success');
        $canceling=Order::where('status', 'canceling')->orderBy('id', 'desc')->paginate(10, ['*'], 'canceling');
        $cancelled=Order::where('status', 'cancelled')->orderBy('id', 'desc')->paginate(10, ['*'], 'cancelled');

        $cancelDetail = OrderCancelation::orderBy('id', 'desc')->get();

        return view('admin.order.manage', compact(['orders', 'pending', 'submitted', 'success'], ['canceling','cancelled','cancelDetail']));
    }

    public function approve(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($request->id);
            $orderItems = OrderItem::where('order_id', $request->id)->get();

            $libraryController = new LibraryController(); // Create instance

            foreach ($orderItems as $item) {
                $libraryController->add($order->user_id, $item->product_id);
            }

            $order->status = 'success';
            $order->save();

            DB::commit();
            return redirect()->back()->with('success', 'Order approved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function remove(Request $request)
    {
        
    }
}
