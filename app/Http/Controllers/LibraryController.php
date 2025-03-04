<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Library;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request) {
        $library=Library::where('id', auth()->id())->orderBy('id', 'desc')->paginate(10);
        return view('shop.library', compact('library'));
    }

    public function manage(Request $request) {
        $library=Library::orderBy('id', 'desc')->paginate(10);
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
