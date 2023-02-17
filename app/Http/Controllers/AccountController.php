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
            'fullname' => 'required',
        ],
            [
                'phone.size' => 'Phone_must_be_13_characters',
                'fullname.required' => 'Fullname_is_required',
                'photo.max' => 'Photo_size_must_be_less_than_2MB',
            ]);

            $data=$request->all();
        $user = $request->user();
        $user->age=$data['age'];
        $user->save();
        return response()->json([
            'status' => true,
            'message' => 'Account_updated_successfully',
            'data' => $user,
            "data2" => $data
        ], 200);
        $user->update($data);
        $user->save();
        $oldPhoto=$user->photo;

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
            'message' => 'Account_updated_successfully',
            'data' => $user
        ], 200);
    }


    //update user password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required | min:6',
        ],
            [
                'old_password.required' => 'Old_password_is_required',
                'new_password.required' => 'New_password_is_required',
                'new_password.min' => 'New_password_must_be_at_least_6_characters',
            ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password))
            return response()->json([
                'status' => false,
                'message' => 'Old_password_is_wrong',
            ], 200);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password_updated_successfully',
        ], 200);
    }
}
