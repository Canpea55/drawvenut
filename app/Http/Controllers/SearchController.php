<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function liveSearch(Request $request)
    {
        $query = $request->get('query');

        // Use a simple LIKE query for live search and select specific fields
        $products = Product::where(column: 'name', operator: 'LIKE', value: "%{$query}%")
            ->select('id', 'name', 'price')
            ->get();
        
        return response()->json($products);
    }
}
