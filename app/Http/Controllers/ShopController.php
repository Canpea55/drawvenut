<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Library;
use App\Models\ProductOptions;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::orderByDesc('id')
            ->where('isPublish', true)
            ->take(9)
            ->get();

        foreach ($products as $product) {
            $options = ProductOptions::where('product_id', $product->id)->get();
            if ($options->isNotEmpty()) {
                $lowest = $options->min('price');
                $highest = $options->max('price');
                $product->lowestPrice = $lowest;
                $product->highestPrice = $highest;
                $product->prices = $lowest == $highest ? "$lowest" : "$lowest-$highest";

                $temp = json_decode($product->images, true);
                foreach ($options as $option) {
                    $temp[] = $option->thumbnail;
                }
                $product->images = json_encode($temp);
            } else {
                $product->lowestPrice = null;
                $product->highestPrice = null;
                $product->prices = "0";
            }
        }

        return view('shop.index', compact('products'));
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
        $product->owned = false;
        foreach ($library as $item) {
            if ($item->product_id == $id) {
                $product->owned = true;
                break;
            }
        }

        $product->options = ProductOptions::where('product_id', $id)->get();

        // Get product options and calculate price range
        $options = ProductOptions::where('product_id', $id)->get();
        if ($options->isNotEmpty()) {
            $lowest = $options->min('price');
            $highest = $options->max('price');
            $product->lowestPrice = $lowest;
            $product->highestPrice = $highest;
            $product->prices = $lowest == $highest ? "$lowest" : "$lowest-$highest";

            $temp = json_decode($product->images, true);
            foreach ($options as $option) {
                $temp[] = $option->thumbnail;
            }
            $product->images = json_encode($temp);
        } else {
            $product->lowestPrice = null;
            $product->highestPrice = null;
            $product->prices = "0";
        }

        return view('shop.product', compact('product'));
    }

    function inventory()
    {
        return view('shop.inventory');
    }
}
