<?php

namespace App\Http\Controllers;

use App\Models\ProductOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Show cart
    public function cart()
    {
        if(session('cart')){
            $cart_items = session()->get('cart', []);
            // Compact selected options with their details for each product in cart
            foreach ($cart_items as $productId => $item) {
                $selectedOptionsDetails = [];
                foreach ($item['selectedOptions'] as $optionId) {
                    $option = ProductOptions::find($optionId);
                    if ($option) {
                        $selectedOptionsDetails[] = [
                            'option_id' => $option->id,
                            'name' => $option->name,
                            'price' => $option->price,
                            'thumbnail' => $option->thumbnail,
                        ];
                    }
                }
                $cart_items[$productId]['optionDetails'] = $selectedOptionsDetails;
            }
            return view('shop.cart', compact('cart_items'));
        } else {
            return redirect(route('index'))->with('danger', 'ยังไม่มีสินค้าในตะกร้าสินค้า');
        }
    }

    // Add item to cart
    public function add(Request $request){
        $cart = session()->get('cart', []);
        if($request->has('option_id') && $request->option_id !== ""){
            $selectedOptions = $cart[$request->product_id]['selectedOptions'] ?? [];
            
            if (!in_array($request->option_id, $selectedOptions)) {
                $selectedOptions[] = $request->option_id;
            } else {
                return redirect()->back()->with('danger', 'สินค้าและตัวเลือกนี้ถูกเพิ่มไปแล้ว');
            }

            if (isset($cart[$request->product_id])) {
                $cart[$request->product_id]['selectedOptions'] = $selectedOptions;
            } else {
                $cart[$request->product_id] = [
                    "name" => $request->product_name,
                    "image" => $request->product_image,
                    "selectedOptions" => $selectedOptions,
                ];
            }

            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'สินค้าถูกเพิ่มลงตะกร้าแล้ว!');
        } else {
            return redirect()->back()->with('danger', 'ไม่พบเจอตัวเลือกสินค้า (option_id)');
        }
    }

    // Remove item
    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        $selectedOptions = $cart[$request->product_id]['selectedOptions'];
        $cart[$request->product_id]['selectedOptions'] = array_values(array_filter($selectedOptions, function($option) use ($request) {
            return $option != $request->option_id;
        }));
        
        // If no options left, remove the product entirely
        if (empty($cart[$request->product_id]['selectedOptions'])) {
            unset($cart[$request->product_id]);
        }
        
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'สินค้าถูกลบออกแล้ว!');
    }

    // Clear cart
    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'ตะกร้าว่างแล้ว!');
    }
}

