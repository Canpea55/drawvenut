<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::orderByDesc('id')->where('isPublish', true)->paginate(9);
        return view('shop.index',compact('products'));
    }

    function about()
    {
        $name = "ก้องไข่เข็ม";
        $date = "19 Sep 2024";
        return view('shop.about', compact('name', 'date'));
    }

    function product($id)
    {
        $product = Product::find($id);
        return view('shop.product', compact('product'));
    }

    function inventory()
    {
        return view('shop.inventory');
    }
}
