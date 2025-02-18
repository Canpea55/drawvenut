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

    // function index()
    // {
    //     $blogs=Blog::paginate(5);
    //     return view('blog', compact('blogs'));
    // }

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
        return redirect(route('form'));
    }

    function productList(Request $request)
    {
        $products=Product::paginate(10);
        return view('/admin/list', compact('products'));
    }

    function delete($id)
    {
        Blog::find($id)->delete();
        return redirect()->back();
    }

    function change($id)
    {
        $blog = Blog::find($id);
        $data = [
            'status' => !$blog->status
        ];
        Blog::find($id)->update($data);
        return redirect()->back();
    }

    function edit($id)
    {
        $blog = Blog::find($id);
        return view('edit', compact('blog'));
    }

    function update(Request $request, $id)
    {
        $request->validate(
            [
                'title'=>'required|max:16',
                'content'=>'required'
            ],
            [
                'title.required'=>'กรุณาป้อนชื่อบทความก่อน',
                'title.max'=>'กรุณาอย่าตั้งชื่อเกิน 16 ตัวอักศร',
                'content.required'=>'กรุณาป้อนบทความของคุณก่อนส่ง'
            ]
        );
        $data = [
            'title'=>$request->title,
            'content'=>$request->content
        ];
        Blog::find($id)->update($data);
        return redirect('/blog');
    }
}
