<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Tag;
use App\Models\ProductOptions;

class SearchController extends Controller
{
    
    public function live(Request $request)
    {
        $query = $request->get('query');

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->where('isPublish', true)
            ->take(3)
            ->get();

            foreach ($products as $product) {
                $options = ProductOptions::where('product_id', $product->id)->get();
                if ($options->isNotEmpty()) {
                    $lowest = $options->min('price');
                    $highest = $options->max('price');
                    $product->lowestPrice = $lowest;
                    $product->highestPrice = $highest;
                    $product->prices = $lowest == $highest ? "$lowest" : "$lowest-$highest";

                    $temp = json_decode($product->images, true);
                    foreach ($options as $option) {
                        $temp[] = $option->thumbnail;
                    }
                    $product->images = json_encode($temp);
                    $image = !empty($temp) ? asset($temp[0]) : null;
                } else {
                    $product->lowestPrice = null;
                    $product->highestPrice = null;
                    $product->prices = "0";
                }
                $product->image = $image;
            }

        $tags = Tag::where('name', 'LIKE', "%{$query}%")
            ->take(7)
            ->get()
            ->map(function ($tag) {
                return [
                    'id'   => $tag->id,
                    'name' => $tag->name,
                    'url'  => route('search', ['keyword' => $tag->name]),
                ];
            });
        
        return response()->json([
            'products' => $products,
            'tags'     => $tags,
        ]);
    }

    public function search(Request $request)
    {
        $keyword = $request->keyword;
        $products = [];
        
        if ($keyword) {
            $tagProducts = Product::whereHas('tags', function($query) use ($keyword) {
            $query->where('name', $keyword);
            })->where('isPublish', true)->orderBy('id', 'desc')->get();
            
            $nameProducts = Product::where('name', 'LIKE', "%{$keyword}%")
            ->where('isPublish', true)
            ->orderBy('id', 'desc')
            ->get();
            $products = $tagProducts->merge($nameProducts)->unique('id');
            
            $perPage = 16;
            $page = request()->get('page', 1);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
            );
            if (!empty($products)) {
            $products->appends(request()->query());
            }
        } else {
            $products = Product::where('isPublish', true)
                ->orderBy('id', 'desc')
                ->paginate(16)
                ->appends(request()->query());
        }
        foreach ($products as $product) {
            $options = ProductOptions::where('product_id', $product->id)->get();
            if ($options->isNotEmpty()) {
                $lowest = $options->min('price');
                $highest = $options->max('price');
                $product->lowestPrice = $lowest;
                $product->highestPrice = $highest;
                $product->prices = $lowest == $highest ? "$lowest" : "$lowest-$highest";

                $temp = json_decode($product->images, true);
                foreach ($options as $option) {
                    $temp[] = $option->thumbnail;
                }
                $product->images = json_encode($temp);
            } else {
                $product->lowestPrice = null;
                $product->highestPrice = null;
                $product->prices = "0";
            }
        }

        return view('shop.search', [
            'products' => $products,
            'keyword' => $keyword
        ]);
    }
}
