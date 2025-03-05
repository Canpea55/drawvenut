<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
