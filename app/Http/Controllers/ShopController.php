<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        return view('home');
    }

    function about()
    {
        $name = "ก้องไข่เข็ม";
        $date = "19 Sep 2024";
        return view('about', compact('name', 'date'));
    }
    function store()
    {
        $products = Product::orderByDesc('id')->where('isPublish', true)->get();
        return view('store',compact('products'));
    }

    function product($id)
    {
        $product = Product::find($id);
        return view('product', compact('product'));
    }

    function inventory()
    {
        return view('inventory');
    }
    
}
