<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Product;
use App\Models\Favorite;
use App\Models\ProductOptionDownloads;
use App\Models\ProductOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $library = Library::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        $products = [];
        $product_options = [];
        $product_option_ids = [];

        foreach ($library as $item) {
            $option = ProductOptions::find($item->product_options_id);
            if ($option) {
                $product_options[] = $option;
                $product_option_ids[] = $option->id;
            }
        }

        $product_ids = [];
        foreach ($product_options as $item) {
            $product_ids[] = $item->product_id;
        }
        $products = Product::whereIn('id', $product_ids)->get();

        foreach($products as $product) {
            $temp = json_decode($product->images, true);
            $options = ProductOptions::where(['product_id' => $product->id])->whereIn('id', $product_option_ids)->get();
            foreach ($options as $option) {
                $temp[] = $option->thumbnail;
            }
            $product->images = json_encode($temp);
        }

        return view('shop.library.index', compact(['library', 'products']));
    }

    function info($product_id)
    {
        $library = Library::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        $options_id = [];
        foreach($library as $item){
            $options_id[] = $item->product_options_id;
        }
        $product = Product::find($product_id);
        $options = $product->options()->whereIn('id', $options_id)->get();

        $temp = json_decode($product->images, true);
        foreach ($options as $option) {
            $temp[] = $option->thumbnail;
        }
        $product->images = json_encode($temp);

        return view('shop.library.info', compact('product', 'options'));
    }

    public function manage(Request $request)
    {
        $library = Library::orderBy('id', 'desc')->paginate(10);
        return view('admin.library.manage', compact('library'));
    }

    public function add($user_id, $product_options_id)
    {
        $item = Library::create([
            'user_id' => $user_id,
            'product_options_id' => $product_options_id,
        ]);
    }

    public function remove(Request $request)
    {

    }

    //toggle favorite item
    public function favorite(Request $request){
        $user_id = Auth::user()->id;
        $product_id = $request->product_id;
        $exist = Favorite::where(['user_id' => $user_id, 'product_id' => $product_id])->first();
        if($exist) {
            $exist->delete();
            if($request->ajax()){
                return response()->json(['favorite' => 'removed']);
            }
            return redirect()->back()->with('favorite', 'removed');
        } else {
            Favorite::create([
                'user_id' => $user_id,
                'product_id' => $product_id,
            ]);
            if($request->ajax()){
                return response()->json(['favorite' => 'added']);
            }
            return redirect()->back()->with('favorite', 'added');
        }
    }

    //get favorite list of current auth
    public function favorites(Request $request){
        $user_id = Auth::user()->id;
        $products = Product::join('favorite', 'favorite.product_id', '=', 'products.id')
            ->where('products.isPublish', true)
            ->where('favorite.user_id', $user_id)
            ->orderBy('favorite.created_at', 'desc')
            ->select('products.*')
            ->paginate(10);
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
        return view('shop.favorite', compact('products'));
    }
}
