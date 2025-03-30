<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Library;
use App\Models\ProductOptions;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
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
        $product = Product::find($id);
        $library = Library::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        $product->owned = false;
        foreach ($library as $item) {
            if ($item->product_id == $id) {
                $product->owned = true;
                break;
            }
        }

        if ($product->options->isNotEmpty()) {
            $lowest = $product->options->min('price');
            $highest = $product->options->max('price');
            $product->lowestPrice = $lowest;
            $product->highestPrice = $highest;
            $product->prices = $lowest == $highest ? "$lowest" : "$lowest-$highest";

            $temp = json_decode($product->images, true);
            foreach ($product->options as $option) {
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

    function free2Library(Request $request){
        dd($request->product_id);
        $product = Product::find($request->product_id);
        if ($product->options->isNotEmpty()) {
            $lowest = $product->options->min('price');
            $highest = $product->options->max('price');
            $product->lowestPrice = $lowest;
            $product->highestPrice = $highest;
            $product->prices = $lowest == $highest ? "$lowest" : "$lowest-$highest";

            $temp = json_decode($product->images, true);
            foreach ($product->options as $option) {
                $temp[] = $option->thumbnail;
            }
            $product->images = json_encode($temp);
        } else {
            $product->lowestPrice = null;
            $product->highestPrice = null;
            $product->prices = "0";
        }

        if($product->prices <= 0){
            DB::beginTransaction();
            try {
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'total' => 0,
                    'status' => "success",
                ]);

                $orderItems = [];
                foreach($product->options as $id => $item) {
                    $orderItems[] = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_options_id' => $item->id,
                        'option_thumbnail' => $item->thumbnail,
                        'option_name' => $item->name,
                        'option_price' => $item->price,
                    ]);
                };
                
                $libraryController = new LibraryController(); // Create instance

                foreach ($orderItems as $item) {
                    $libraryController->add($order->user_id, $item->product_options_id);
                }
                
                DB::commit();
                return redirect(route('order',  $order->id))->with('success', 'Ordered successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return dd($e);
                // return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
    }
}
