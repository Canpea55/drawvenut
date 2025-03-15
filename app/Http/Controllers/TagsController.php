<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function search(Request $request) {
        $query = $request->get('query');
        $tags = \App\Models\Tag::where('name', 'LIKE', "%{$query}%")
                            ->pluck('name');
        return response()->json($tags);
    }
}
