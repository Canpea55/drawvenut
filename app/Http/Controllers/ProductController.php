<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductTags;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Library;
use App\Models\Tag;
use Spatie\Tags\HasTags;

class ProductController extends Controller
{
    function add()
    {
        return view('admin/products/form');
    }

    function insert(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'price' => 'required',
            ],
            [
                'name.required' => 'กรุณาป้อนชื่อสินค้าก่อน',
                'price.required' => 'กรุณาป้อนราคาด้วย (อย่างน้อย 0 บาท)',
            ]
        );

        // Handle Image Upload
        $storedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                
                // Get the original file name
                $originalName = $image->getClientOriginalName();
                
                $storedImages[] = $image->storeAs('images/products', $originalName, 'public');
                
                // $storedImages[] = $image->store('products', 'public'); // Save image and store path
            }
        }        

        // Decode the image order from JSON and sort the uploaded images accordingly
        $imageOrder = json_decode($request->image_order, true);
        if (!empty($imageOrder)) {
            usort($storedImages, function ($a, $b) use ($imageOrder) {
                return array_search(basename($a), $imageOrder) - array_search(basename($b), $imageOrder);
            });
        }

        // Create product
        $product = Product::create([
            'name'=>$request->name,
            'price'=>$request->price,
            'description'=> $request->description,
            'images' => json_encode($storedImages),
            'isBuyable' => $request->has('isBuyable'),
            'isDownloadable'=> $request->has('isDownloadable'),
            'isPublish' => $request->has('isPublish'),
            'download' => $request->download,
        ]);

        // Custom Tag processing
        if ($request->filled('tags')) {
            // Split the tags string by spaces
            $tags = preg_split('/\s+/', trim($request->input('tags')));
            $tagIds = [];
            foreach ($tags as $tagName) {
                $tagName = trim($tagName);
                if ($tagName) {
                    // Find the tag by name or create it if it does not exist
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
            }
            // Sync tags (attach these tags to the product)
            $product->tags()->sync($tagIds);
        }

        if($request->input("createMultiple")) {
            return $this->add()->with('success', 'Product added successfully!');
        }
        return redirect(route('product.manage'))->with('success', 'Product added successfully!');
    }

    function update(Request $request, $id)
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
        
        // Handle Image Upload
        $storedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                
                // Get the original file name
                $originalName = $image->getClientOriginalName();
                
                $storedImages[] = $image->storeAs('images/products', $originalName, 'public');
            }
        }

        // Decode the image order from JSON and sort the uploaded images accordingly
        $imageOrder = json_decode($request->image_order, true);
        if(empty($storedImages))
        {
            foreach($imageOrder as &$image)
            {
                $image = str_replace("new_images/", "images/", $image);
            }
            $storedImages = $imageOrder;
        }
        else
        {
            if (!empty($imageOrder)) {
                usort($storedImages, function ($a, $b) use ($imageOrder) {
                    return array_search(basename($a), $imageOrder) - array_search(basename($b), $imageOrder);
                });
            }
        }

        $productData = [
            'name'=>$request->name,
            'price'=>$request->price,
            'description'=> $request->description,
            'images' => json_encode($storedImages),
            'isBuyable' => $request->has('isBuyable'),
            'isDownloadable'=> $request->has('isDownloadable'),
            'isPublish' => $request->has('isPublish'),
            'download' => $request->download,
        ];
        $product = Product::findOrFail($id);
        $product->update($productData);
        
        // Custom Tag processing for update
        if ($request->filled('tags')) {
            $tags = preg_split('/\s+/', trim($request->input('tags')));
            $tagIds = [];
            foreach ($tags as $tagName) {
                $tagName = trim($tagName);
                if ($tagName) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
            }
            $product->tags()->sync($tagIds);
        } else {
            // Remove all tags if none are provided
            $product->tags()->sync([]);
        }

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    function manage(Request $request)
    {
        $products=Product::orderBy('id', 'desc')->paginate(10);
        return view('/admin/products/manage', compact('products'));
    }

    function delete($id)
    {
        Product::find($id)->delete();
        return redirect()->back();
    }

    function isPublish($id)
    {
        $product = Product::find($id);
        $data = [
            'isPublish' => !$product->isPublish
        ];
        Product::find($id)->update($data);
        return redirect()->back();
    }
    function isBuyable($id)
    {
        $product = Product::find($id);
        $data = [
            'isBuyable' => !$product->isBuyable
        ];
        Product::find($id)->update($data);
        return redirect()->back();
    }
    function isDownloadable($id)
    {
        $product = Product::find($id);
        $data = [
            'isDownloadable' => !$product->isDownloadable
        ];
        Product::find($id)->update($data);
        return redirect()->back();
    }

    function edit($id)
    {
        $product = Product::find($id);
        $tags = Tag::whereIn('id', function($query) use ($id) {
            $query->select('tag_id')
              ->from('products_tags')
              ->where('product_id', $id);
        })->pluck('name')->implode(' ');

        return view('admin/products/form', compact(['product', 'tags']));
    }
}
