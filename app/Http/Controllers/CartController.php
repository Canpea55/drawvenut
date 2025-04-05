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
            session()->put('cart', $cart_items);
            return view('shop.cart');
        } else {
            return redirect(route('index'))->with([
                'danger' => 'ตะกร้าสินค้าว่างเปล่า!',
                'toast-icon' => asset('icons/oval-interface/shopping-bag.svg'),
            ]);
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
                return redirect()->back()->with([
                    'danger' => 'สินค้าและตัวเลือกนี้ถูกเพิ่มไปแล้ว',
                    'toast-icon' => asset('icons/oval-interface/shopping-bag.svg'),
                ]);
            }

            if (isset($cart[$request->product_id])) {
                $cart[$request->product_id]['selectedOptions'] = $selectedOptions;
            } else {
                $cart[$request->product_id] = [
                    "name" => $request->product_name,
                    "selectedOptions" => $selectedOptions,
                ];
            }

            session()->put('cart', $cart);
            return redirect()->back()->with([
                'success' => 'เพิ่มสินค้าลงตะกร้าเรียบร้อยค่ะ!',
                'toast-icon' => asset('icons/oval-interface/shopping-bag.svg'),
            ]);
        } else {
            return redirect()->back()->with([
                'danger' => 'ไม่พบเจอตัวเลือกสินค้า ' . $request->option_id,
                'toast-icon' => asset('icons/oval-interface/shopping-bag.svg'),
            ]);
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
        return redirect()->back()->with([
            'success' => 'สินค้าถูกลบออกแล้ว!',
            'toast-icon' => asset('icons/oval-interface/shopping-bag.svg'),
        ]);
    }

    // Clear cart
    public function clear()
    {
        session()->forget('cart');
        return redirect(route('index'))->with([
            'success' => 'ตะกร้าว่างแล้ว!',
            'toast-icon' => asset('icons/oval-interface/shopping-bag.svg'),
        ]);
    }
}

