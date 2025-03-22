<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductTags;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Library;
use App\Models\Tag;
use App\Models\ProductOptions;
use App\Models\ProductOptionDownloads;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    function add()
    {
        return view('admin/products/form');
    }

    function insert(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate(
                [
                    'name' => 'required',
                ],
                [
                    'name.required' => 'กรุณาป้อนชื่อสินค้าก่อน',
                ]
            );
    
            // Handle Image Upload
            $storedImages = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    
                    // Get the original file name
                    $originalName = $image->getClientOriginalName();
                    
                    $storedImages[] = $image->storeAs('products', $originalName, 'public');
                    
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
                'description'=> $request->description,
                'images' => json_encode($storedImages),
                'isBuyable' => $request->has('isBuyable'),
                'isDownloadable'=> $request->has('isDownloadable'),
                'isPublish' => $request->has('isPublish'),
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
    
            //Product Option Processing
            if ($request->filled('options') && is_array($request->input('options'))) {
                // First, determine which existing options (numeric keys) were submitted.
                $postedOptionIds = [];
                foreach ($request->input('options') as $optionKey => $optionData) {
                    if (is_numeric($optionKey)) {
                        $postedOptionIds[] = $optionKey;
                    }
                }
                // Delete any existing options for this product that were not submitted.
                ProductOptions::where('product_id', $product->id)
                    ->whereNotIn('id', $postedOptionIds)
                    ->delete();
    
                // Loop through submitted options.
                foreach ($request->input('options') as $optionKey => $optionData) {
                    $name = isset($optionData['name']) ? trim($optionData['name']) : '';
                    
                    // Process the option thumbnail file if provided.
                    $thumbnailPath = null;
                    if ($request->hasFile("options.{$optionKey}.thumbnail")) {
                        $file = $request->file("options.{$optionKey}.thumbnail");
                        $thumbnailPath = $file->storeAs('products', $file->getClientOriginalName(), 'public');
                    }
                    
                    $productOption = ProductOptions::create([
                        'product_id' => $product->id, 
                        'thumbnail'=> $thumbnailPath, 
                        'name' => $name,
                        'price' => $optionData['price'],
                    ]);
    
                    // Process downloads for this option.
                    if (isset($optionData['downloads']) && is_array($optionData['downloads'])) {
                        // Remove all downloads for this option.
                        ProductOptionDownloads::where('product_options_id', $productOption->id)->delete();
                        
                        // Iterate over each submitted download.
                        foreach ($optionData['downloads'] as $key => $downloadData) {
                            $link = $downloadData['link'];
                            $type = $downloadData['type'];
                            $isDownload = isset($downloadData['isDownload']);
    
                            if (empty($link)) {
                                continue;
                            } else {
                                ProductOptionDownloads::create([
                                    'product_options_id' => $productOption->id,
                                    'link' => $link,
                                    'type' => $type,
                                    'isDownload' => $isDownload,
                                ]);
                            }
                        }
                    } else {
                        // If no downloads were provided for this option, ensure any existing downloads are removed.
                        ProductOptionDownloads::where('product_options_id', $productOption->id)->delete();
                    }
                }
            }
    
            // if($request->input("createMultiple")) {
            //     return $this->add()->with('success', 'Product added successfully!');
            // }

            DB::commit();
            return redirect(route('product.manage'))->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate(
                [
                    'name'=>'required',
                ],
                [
                    'name.required'=>'กรุณาป้อนชื่อสินค้าก่อน',
                ]
            );
            
            // Handle Image Upload
            $storedImages = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    
                    // Get the original file name
                    $originalName = $image->getClientOriginalName();
                    
                    $storedImages[] = $image->storeAs('products', $originalName, 'public');
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
                'description'=> $request->description,
                'images' => json_encode($storedImages),
                'isBuyable' => $request->has('isBuyable'),
                'isDownloadable'=> $request->has('isDownloadable'),
                'isPublish' => $request->has('isPublish'),
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
    
            //Product Option Processing
            if ($request->filled('options') && is_array($request->input('options'))) {
                // 1. Collect submitted existing option names.
                $optionNames = [];
                foreach ($request->input('options') as $optionKey => $optionData) {
                    try {
                        $optionNames[] = $optionData['name'];
                    } catch (\Exception $e) {
                        dd($e->getMessage(), $optionData);
                    }
                }
                
                // 2. Delete any options for this product that were not submitted.
                ProductOptions::where('product_id', $product->id)
                    ->whereNotIn('name', $optionNames)
                    ->delete();
            
                // 3. Loop through submitted options.
                foreach ($request->input('options') as $optionKey => $optionData) {
                    // Ensure the option has a non-empty name.
                    $name = isset($optionData['name']) ? trim($optionData['name']) : '';
                    if ($name === '') {
                        continue;
                    }
    
                    // Process option thumbnail if provided.
                    $thumbnailPath = null;
                    if(isset($optionData['existingThumbnail'])) {
                        $thumbnailPath = $optionData['existingThumbnail'];
                    } else {
                        if ($request->hasFile("options.{$optionKey}.thumbnail")) {
                            $file = $request->file("options.{$optionKey}.thumbnail");
                            $thumbnailPath = $file->storeAs('products', $file->getClientOriginalName(), 'public');
                        }
                    }
            
                    // Update an existing option; otherwise, create a new one.
                    $productOption = ProductOptions::where('name', $name)->get()->first();
                    if($productOption){
                        // Update exist option
                        $productOption->update([
                            'product_id' => $product->id, 
                            'thumbnail'=> $thumbnailPath, 
                            'name' => $name,
                            'price' => $optionData['price'],
                        ]);
                    } else {
                        // Create new option
                        $productOption = ProductOptions::create([
                            'product_id' => $product->id, 
                            'thumbnail'=> $thumbnailPath, 
                            'name' => $name,
                            'price' => $optionData['price'],
                        ]);
                    }
            
                    // Process downloads for this option.
                    if (isset($optionData['downloads']) && is_array($optionData['downloads'])) {
                        // Remove all current downloads for this option.
                        ProductOptionDownloads::where('product_options_id', $productOption->id)->delete();
            
                        // Loop through each submitted download.
                        foreach ($optionData['downloads'] as $key => $downloadData) {
                            $link = $downloadData['link'];
                            $type = $downloadData['type'];
                            $isDownload = isset($downloadData['isDownload']);
            
                            // Skip if link is empty.
                            if (empty($link)) {
                                continue;
                            } else {
                                ProductOptionDownloads::create([
                                    'product_options_id' => $productOption->id,
                                    'link'               => $link,
                                    'type'               => $type,
                                    'isDownload'         => $isDownload,
                                ]);
                            }
                        }
                    } else {
                        // No downloads submitted – ensure any existing downloads are removed.
                        ProductOptionDownloads::where('product_options_id', $productOption->id)->delete();
                    }
                }
            } else {
                ProductOptions::where('product_id', $product->id)->delete();
                // Delete downloads for options that no longer exist
                ProductOptionDownloads::whereIn('product_options_id', function($query) use ($product) {
                    $query->select('id')
                        ->from('product_options')
                        ->where('product_id', $product->id);
                })->delete();
            }
    
            DB::commit();
            return redirect()->back()->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    function manage(Request $request)
    {
        $products=Product::orderBy('id', 'desc')->paginate(10);
        foreach ($products as $product) {
            $options = ProductOptions::where('product_id', $product->id)->get();
            if ($options->isNotEmpty()) {
                $lowest = $options->min('price');
                $highest = $options->max('price');
                $product->lowestPrice = $lowest;
                $product->highestPrice = $highest;
                $product->prices = $lowest == $highest ? "$lowest" : "$lowest-$highest";
            } else {
                $product->lowestPrice = null;
                $product->highestPrice = null;
                $product->prices = "0";
            }
        }
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
              ->from('product_tags')
              ->where('product_id', $id);
        })->pluck('name')->implode(' ');
        $options = ProductOptions::where(['product_id' => $id])->get();

        return view('admin/products/form', compact(['product', 'tags', 'options']));
    }
}
