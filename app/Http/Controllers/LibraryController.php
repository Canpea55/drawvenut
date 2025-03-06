<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Product;
use Illuminate\Http\Request;

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

        return view('shop.library', compact(['library', 'products']));
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
}
