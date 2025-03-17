<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Library;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::orderByDesc('id')
        ->where('isPublish', true)
        ->take(9)
        ->get();
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
        // Eager load tags with the product
        $product = Product::with('tags')->find($id);
        $library = Library::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        $owned = false;
        foreach ($library as $item) {
            if ($item->product_id == $id) {
                $owned = true;
                break;
            }
        }
        return view('shop.product', compact('product', 'owned'));
    }

    function inventory()
    {
        return view('shop.inventory');
    }
}
