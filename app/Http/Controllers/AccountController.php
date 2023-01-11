<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    //update user account
    public function updateAccount(Request $request)
    {

        $request->validate([
            'phone' => ' size:13 ',
            'photo' => ' mimes:jpeg,jpg,png | max:1000',
        ]);

        $user = $request->user();
        $user->fullname = $request->fullname;
        $user->phone = $request->phone;
        $user->age = $request->age;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Account updated successfully',
            'data' => $user
        ], 200);
    }


    //update user password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required | min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password))
            return response()->json([
                'status' => false,
                'message' => 'Old password is wrong',
            ], 200);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully',
        ], 200);
    }
}
