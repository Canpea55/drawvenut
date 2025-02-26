<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //
    //Product Management System
    //
    function productCreate()
    {
        return view('admin/products/create');
    }

    function productInsert(Request $request)
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
            return redirect(route('productList'))->with('success', 'Product added successfully!');
        }
        return redirect(route('productCreate'))->with('success', 'Product added successfully!');
    }

    function productUpdate(Request $request, $id)
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
        return redirect(route('productList'));
    }

    function productList(Request $request)
    {
        $products=Product::orderBy('id', 'desc')->paginate(10);
        return view('/admin/products/list', compact('products'));
    }

    function productDelete($id)
    {
        Product::find($id)->delete();
        return redirect()->back();
    }

    function productToggle($id)
    {
        $product = Product::find($id);
        $data = [
            'isPublish' => !$product->isPublish
        ];
        Product::find($id)->update($data);
        return redirect()->back();
    }

    function productEdit($id)
    {
        $product = Product::find($id);
        return view('admin/products/edit', compact('product'));
    }

    //
    //Users Management System
    //
    function userList(Request $request)
    {
        $users=User::orderBy('updated_at', 'desc')->paginate(10);
        return view('/admin/users/list', compact('users'));
    }
    
    function userEdit($id)
    {
        $user = User::find($id);
        return view('admin/users/edit', compact('user'));
    }

    function userUpdate(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate(
            [
                'name'=>'required|max:255|string',
                'email'=>'required|distinct|max:255|email|string',
                'password'=>'nullable|min:8|confirmed'
            ],
            [
                'name.required'=>'กรุณาป้อนชื่อสินค้าก่อน',
                'name.max'=>'ยาวเกิน 255 ตัวอักษร',
                'email.required'=>'กรุณาป้อนอีเมลด้วย',
                'email.distinct'=>'อีเมลซ้ำกับคนอื่น',
                'email.max'=>'ยาวเกิน 255 ตัวอักษร',
                'password.min'=>'รหัสต้องยาวกว่า 8 ตัว',
                'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
            ]
        );

        if(!empty($request->password))
        {
            Hash::make($request->password);

            $user->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=> $request->password,
                'updated_at'=> now(),
            ]);
        }
        else
        {
            $user->update([
                'name'=>$request->name,
                'email'=>$request->email,
            ]);
        }

        return redirect(route('userList'));
    }

    function userDelete($id)
    {
        User::find($id)->delete();
        return redirect()->back();
    }
}
