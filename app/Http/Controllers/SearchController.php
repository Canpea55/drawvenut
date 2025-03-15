<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Tag;

class SearchController extends Controller
{
    public function live(Request $request)
    {
        $query = $request->get('query');

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'price', 'images')
            ->take(3)
            ->get()
            ->map(function ($product) {
                $images = json_decode($product->images);
                $firstImage = !empty($images) ? asset('storage/' . $images[0]) : null;
                return [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'price' => $product->price,
                    'image' => $firstImage,
                ];
            });

        $tags = Tag::where('name', 'LIKE', "%{$query}%")
            ->take(7)
            ->get()
            ->map(function ($tag) {
                return [
                    'id'   => $tag->id,
                    'name' => $tag->name,
                    'url'  => route('search', ['tag' => $tag->name]),
                ];
            });
        
        return response()->json([
            'products' => $products,
            'tags'     => $tags,
        ]);
    }

    public function search(Request $request)
    {
        // In your search view, you can check for a tag via $request->tag and query products accordingly.
        return view('shop.search');
    }
}
