<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Show cart
    public function cart()
    {
        $cart = session()->get('cart', []);
        return view('shop.cart', compact('cart'));
    }

    // Add item to cart
    public function add(Request $request){
        $cart = session()->get('cart', []);

        $id = $request->id;
        if (isset($cart[$id])) {
            
        } else {
            $cart[$id] = [
                "name" => $request->name,
                "image" => $request->image,
                "price" => $request->price,
                "description" => $request->description,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'สินค้าถูกเพิ่มลงตะกร้าแล้ว!');
    }

    // Remove item
    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        unset($cart[$request->id]);
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'สินค้าถูกลบออกแล้ว!');
    }

    // Clear cart
    public function clear()
    {
        session()->forget('cart');
        // return redirect()->back()->with('success', 'ตะกร้าว่างแล้ว!');
        return redirect('/')->with('success', 'ตะกร้าว่างแล้ว!');
    }
}

