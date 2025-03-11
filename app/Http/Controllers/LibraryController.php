<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $library = Library::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        $products = [];

        foreach ($library as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $products[] = $product;
            }
        }

        return view('shop.library.index', compact(['library', 'products']));
    }

    function info($product_id)
    {
        $product = Product::find($product_id);
        $library=Library::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        $owned = false;
        foreach($library as $item) {
            if($item->product_id == $product_id) {
                $owned = true;
            }
        }
        return view('shop.library.product-info', compact(['product', 'owned']));
    }

    public function manage(Request $request)
    {
        $library = Library::orderBy('id', 'desc')->paginate(10);
        return view('admin.library.manage', compact('library'));
    }

    public function add($user_id, $product_id)
    {
        $item = Library::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
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
            return redirect()->back()->with('favorite', 'removed');
        } else {
            Favorite::create([
                'user_id' => $user_id,
                'product_id' => $product_id,
            ]);
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
        return view('shop.favorite', compact('products'));
    }
}
