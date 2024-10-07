<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    function index()
    {
        $logo = Storage::url("images/logo.png");
        $blogs = Blog::orderByDesc('id')->where('status', true)->get();
        return view('home', compact('blogs'));
    }

    function detail($id)
    {
        $blog = Blog::find($id);
        return view('detail', compact('blog'));
    }
}
