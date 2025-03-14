<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function liveSearch(Request $request)
    {
        $query = $request->get('query');

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'price', 'images')
            ->take(3)  // This will limit to first 3 results
            ->get()
            ->map(function ($product) {
                // Decode the JSON string to array
                $images = json_decode($product->images);
                
                // Get the first image if available
                $firstImage = !empty($images) ? asset('storage/' . $images[0]) : null;
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $firstImage
                ];
            });
        
        return response()->json($products);
    }
}
