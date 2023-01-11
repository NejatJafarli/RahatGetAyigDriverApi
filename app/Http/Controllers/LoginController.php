<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Driver;

class LoginController extends Controller
{
    public function adminDashboard()
    {
        $users = Admin::all();
        $success =  $users;

        return response()->json($success, 200);
    }
    public function hello(Request $request){
        return response()->json(['status' => false, 'error' => 'Phone and Password are Wrong.'], 200);

    }

    public function driverLogin(Request $request)
    {
        $request->validate(
            [
                'phone' => 'required',
                'password' => 'required',
            ],
            [
                'phone.required' => 'Phone is required',
                'password.required' => 'Password is required',
            ]
        );

        if (auth()->guard('driver')->attempt(['phone' => $request->phone, 'password' => $request->password])) {

            config(['auth.guards.api.provider' => 'driver']);

            $user = Driver::find(auth()->guard('driver')->user()->id);
            $success['status'] =  true;
            $success['user'] =  $user;
            $success['token'] =  $user->createToken('MyApp', ['driver'])->accessToken;

            return response()->json($success, 200);
        } else {
            return response()->json(['status' => false, 'error' => 'Phone and Password are Wrong.'], 200);
        }
    }
    public function checktoken()
    {
        $success['message'] =  "success";

        return response()->json($success, 200);
    }
    public function driverLogout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
        // $request->user()->token()->revoke();
        // return response()->json([
        //     'message' => 'Successfully logged out'
        // ]);
    }
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required | email',
            'password' => 'required',
        ]);

        if (auth()->guard('admin')->attempt(['phone' => $request->phone, 'password' => $request->password])) {

            config(['auth.guards.api.provider' => 'admin']);

            $admin = Admin::find(auth()->guard('admin')->user()->id);
            $success['token'] =  $admin->createToken('MyApp', ['admin'])->accessToken;
            $success['status'] =  true;
            $success['user'] =  $admin;

            return response()->json($success, 200);
        } else {
            return response()->json(['status' => false, 'error' => 'Email and Password are Wrong.'], 200);
        }
    }
}
