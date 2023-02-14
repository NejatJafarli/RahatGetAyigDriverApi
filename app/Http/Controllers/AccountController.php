<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Driver;

class AccountController extends Controller
{
    //update user account
    public function updateAccount(Request $request)
    {
        $request->validate([
            'phone' => ' size:13 ',
            'photo' => 'max:2048',
        ]);

        $user = $request->user();
        $user->fullname = $request->fullname;
        $user->phone = $request->phone;
        if ($request->age == null) 
            $user->age = null;
        else
            $user->age = $request->age;
        $user->save();

        $oldPhoto=$request->user()->photo;

        if ($request->hasFile('photo')) {
           
            $photo = $request->file('photo');
            //send PHOTO FILE TO https://user.rahatget.az/api/admin/drivers/MovePhotoDriver URL
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://user.rahatget.az/api/MovePhotoDriver', [
                'multipart' => [
                    [
                        'name'     => 'photo',
                        'contents' => fopen($photo, 'r'),
                        'filename' => $photo->getClientOriginalName()
                    ],
                    //driver id
                    [
                        'name'     => 'DriverId',
                        'contents' => $user->id
                    ],
                ]
            ]);

            //get response from https://user.rahatget.az/api/admin/drivers/MovePhotoDriver
            $response = json_decode($response->getBody()->getContents());
        } 
        $user=Driver::find($user->id);
        //MOVE TO rahatget/user/public/uploads/drivers
        $user->photo=base64_encode(file_get_contents("https://user.rahatget.az/uploads/drivers/".$user->photo));
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
