<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Blog;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function index()
    {
        $blogs=Blog::paginate(5);
        return view('blog', compact('blogs'));
    }

    function createBlog()
    {
        return view('form');
    }

    function insert(Request $request)
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
        $data=[
            'title'=>$request->title,
            'content'=>$request->content
        ];
        Blog::insert($data);
        return redirect('/blog');
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
