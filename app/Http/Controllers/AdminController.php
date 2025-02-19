<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Blog;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function productForm()
    {
        return view('/admin/form');
    }

    function productInsert(Request $request)
    {
        $request->validate(
            [
                'name'=>'required',
                'price'=>'required',
            ],
            [
                'name.required'=>'กรุณาป้อนชื่อสินค้าก่อน',
                'price.required'=>'กรุณาป้อนราคาด้วย (อย่างน้อย 0 บาท)',
                // 'price.min'=>'ราคาอย่างน้อยต้องมากกว่าหรือเท่ากับ 0',
            ]
        );
        $data=[
            'name'=>$request->name,
            'price'=>$request->price,
            'description'=> $request->description,
            'isBuyable' => $request->has('isBuyable'),
            'isDownloadable'=> $request->has('isDownloadable'),
            'isPublish' => $request->has('isPublish'),
        ];
        Product::insert($data);
        return redirect(route('productForm'));
    }

    function productList(Request $request)
    {
        $products=Product::paginate(10);
        return view('/admin/list', compact('products'));
    }

    function productDelete($id)
    {
        Product::find($id)->delete();
        return redirect()->back();
    }

    function productToggle($id)
    {
        $product = Product::find($id);
        $data = [
            'isPublish' => !$product->isPublish
        ];
        Product::find($id)->update($data);
        return redirect()->back();
    }

    function productEdit($id)
    {
        $product = Product::find($id);
        return view('admin.edit', compact('product'));
    }

    function productUpdate(Request $request, $id)
    {
        $request->validate(
            [
                'name'=>'required',
                'price'=>'required',
            ],
            [
                'name.required'=>'กรุณาป้อนชื่อสินค้าก่อน',
                'price.required'=>'กรุณาป้อนราคาด้วย (อย่างน้อย 0 บาท)',
                // 'price.min'=>'ราคาอย่างน้อยต้องมากกว่าหรือเท่ากับ 0',
            ]
        );
        $data=[
            'name'=>$request->name,
            'price'=>$request->price,
            'description'=> $request->description,
            'isBuyable' => $request->has('isBuyable'),
            'isDownloadable'=> $request->has('isDownloadable'),
            'isPublish' => $request->has('isPublish'),
        ];
        Product::find($id)->update($data);
        return redirect(route('productList'));
    }
}
