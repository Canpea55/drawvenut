<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Hamcrest\Number\OrderingComparison;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orders($user_id)
    {
        
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

    public function remove(Request $request)
    {
        
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
}
