<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Library;

class ProductController extends Controller
{
    function create()
    {
        return view('admin/products/create');
    }

    function add(Request $request)
    {
        $request->validate(
            [
                'name'=>'required',
                'price'=>'required',
            ],
            [
                'name.required'=>'กรุณาป้อนชื่อสินค้าก่อน',
                'price.required'=>'กรุณาป้อนราคาด้วย (อย่างน้อย 0 บาท)',
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

        if(!$request->input("createMultiple")) {
            return redirect(route('products'))->with('success', 'Product added successfully!');
        }
        return redirect(route('product.create'))->with('success', 'Product added successfully!');
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

        $product = [
            'name'=>$request->name,
            'price'=>$request->price,
            'description'=> $request->description,
            'images' => json_encode($storedImages),
            'isBuyable' => $request->has('isBuyable'),
            'isDownloadable'=> $request->has('isDownloadable'),
            'isPublish' => $request->has('isPublish'),
            'download' => $request->download,
        ];

        Product::find($id)->update($product);
        // return redirect(route('products'));
        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    function products(Request $request)
    {
        $products=Product::orderBy('id', 'desc')->paginate(10);
        return view('/admin/products/list', compact('products'));
    }

    function delete($id)
    {
        Product::find($id)->delete();
        return redirect()->back();
    }

    function toggle($id)
    {
        $product = Product::find($id);
        $data = [
            'isPublish' => !$product->isPublish
        ];
        Product::find($id)->update($data);
        return redirect()->back();
    }

    function edit($id)
    {
        $product = Product::find($id);
        $library=Library::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        $owned = false;
        foreach($library as $item) {
            if($item->product_id == $id) {
                $owned = true;
            }
        }
        return view('admin/products/edit', compact(['product', 'owned']));
    }
}
