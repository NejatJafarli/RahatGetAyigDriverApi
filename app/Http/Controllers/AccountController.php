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
            'photo' => 'max:10240|image',
            'fullname' => 'required',
        ],
            [
                'fullname.required' => 'Fullname_is_required',
                'photo.max' => 'Photo_size_must_be_less_than_10MB',
            'photo.image' => 'Photo_must_be_image',

            ]);

            $base=base_path();
            $BaseBackFolder=basename($base);
            $base=str_replace($BaseBackFolder,"",$base);
            $path=$base."user/public/uploads/drivers";

        $user = $request->user();
        $user->fullname = $request->fullname;
        $user->age = $request->age;
        $user->save();
        $oldPhoto=$user->photo;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            if($photo->getClientOriginalExtension()=="")
                $photoName.= ".jpg";
            
            $photo->move($path, $photoName);
            $user->photo = $photoName;
            if($oldPhoto!='default.png' && $oldPhoto!=null){
                unlink($path.'/'.$oldPhoto);
            }
            $user->save();
        } 
        // $user=Driver::find($user->id);
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
