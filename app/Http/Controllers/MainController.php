<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\rideAyigForUser;
use Illuminate\Http\Request;

class MainController extends Controller
{
    //
    public function getRides(Request $request)
    {
        //where status canceled or completed 
        $rides = rideAyigForUser::where('AyigDriverId', $request->user()->id)
            ->where('status', 'Canceled')->orWhere('status', 'Completed')
            ->orderBy('created_at', 'desc')->get();
        if ($rides == null)
            return response()->json([
                'status' => false,
                'message' => 'Rides_not_found'
            ]);
        else
            return response()->json([
                'status' => true,
                'message' => 'Rides_found',
                'data' => $rides
            ]);
    }
    public function EndUserAyigRide(Request $req)
    {
        $req->validate([
            'rideId' => 'required',
        ],
            [
                'rideId.required' => 'RideId_is_required',
            ]);
        //get rideAyigForUser for rideId
        $ride = rideAyigForUser::find($req->rideId);
        if ($ride == null)
            return [
                'status' => false,
                'message' => 'Ride_not_found'
            ];
        if ($ride->status == 'Canceled')
            return [
                'status' => false,
                'message' => 'Ride_Canceled'
            ];
        else {
            if ($ride->AyigDriverId != $req->user()->id)
                return [
                    'status' => false,
                    'message' => 'You_are_not_the_driver_of_this_ride'
                ];
            $ride->status = 'Completed';
            $ride->save();
            return [
                'status' => true,
                'message' => 'Ride_finished'
            ];
        }
    }
    //create ride Ayig
    public function InfoDriverAyigRide(Request $req)
    {
        $req->validate([
            'rideId' => 'required',
        ],[
            'rideId.required' => 'RideId_is_required',
        ]);
        //get rideAyigForUser for rideId
        $ride = rideAyigForUser::find($req->rideId);
        if ($ride == null)
            return [
                'status' => false,
                'message' => 'Ride_not_found'
            ];
        else
            return [
                'status' => true,
                'message' => 'Ride_found',
                'data' => $ride
            ];
    }

    public function AcceptRideAyig(Request $req)
    {
        $req->validate([
            'rideId' => 'required',
            'OrderId' => 'required'
        ],[
            'rideId.required' => 'RideId_is_required',
            'OrderId.required' => 'OrderId_is_required'
        ]);

        $ride = rideAyigForUser::find($req->rideId);

        if ($ride == null)
            return [
                'status' => false,
                'message' => 'Ride_not_found'
            ];

        if ($ride->status == 'Canceled')
            return [
                'status' => false,
                'message' => 'Ride_Canceled'
            ];
        else if ($ride->status == 'Accepted')
            return [
                'status' => false,
                'message' => 'Ride_Already_Accepted'
            ];
        else if ($ride->status == 'Completed')
            return [
                'status' => false,
                'message' => 'Ride_Already_Completed'
            ];
        else {
            if($ride->status!='rezerv')
            {
                if ($ride->status != 'Pending')
                return [
                    'status' => false,
                    'message' => 'Ride_Not_Pending'
                ];
            }
            $ride->status = 'Accepted';
            $ride->AyigDriverId = $req->user()->id;
            $ride->OrderId = $req->OrderId;
            $ride->save();
            return [
                'status' => true,
                'message' => 'Ride_Accepted'
            ];
        }
    }
    public function DriverIsOnline(Request $req){
        $req->validate([
            'IsOnline' => 'required'
        ],[
            'IsOnline.required' => 'IsOnline_is_required'
        ]);
        $driver = $req->user();
        $driver->online = $req->IsOnline;
        $driver->save();
        return [
            'status' => true,
            'message' => 'Driver_is_online_or_offline'
        ];
    }
    public function getUserInfo(Request $req){
        $req->validate([
            'id' => 'required',
        ]);
        $user = User::find($req->id);
        if (!$user)
            return response()->json([
                'status' => false,
                'message' => 'this_User_is_not_exist',
            ], 200);

        $return=[];
        $return['id']=$user->id;
        $return['fullname']=$user->fullname;
        $image = file_get_contents('https://user.rahatget.az/uploads/users/' . $user->photo);
        $image = base64_encode($image);
        $return['photo'] = 'data:image/jpeg;base64,'.$image;
        $return['phone']=$user->phone;

        return response()->json([
            'status' => true,
            'message' => 'User_found',
            'data' => $return
        ], 200);
    }
    public function ArrivedToCustomerAyig(Request $req)
    {
        $req->validate([
            'rideId' => 'required',
        ]);

        $ride = rideAyigForUser::find($req->rideId);

        if ($ride == null)
            return [
                'status' => false,
                'message' => 'Ride_not_found'
            ];

        if ($ride->status == 'Canceled')
            return [
                'status' => false,
                'message' => 'Ride_Canceled'
            ];
        else if ($ride->status == 'Completed')
            return [
                'status' => false,
                'message' => 'Ride_Already_Completed'
            ];
        else {
            if ($ride->status != 'Accepted')
                return [
                    'status' => false,
                    'message' => 'Ride_Not_Accepted'
                ];
            $ride->status = 'Waiting Customer';
            date_default_timezone_set('Asia/Baku');
            $ride->waitingStart = date('Y-m-d H:i:s');
            $ride->save();
            return [
                'status' => true,
                'message' => 'Ride_Waiting_Customer'
            ];
        }
    }
    public function StartMovingAyigRide(Request $req)
    {
        $req->validate([
            'rideId' => 'required',
        ]);

        $ride = rideAyigForUser::find($req->rideId);

        if ($ride == null)
            return [
                'status' => false,
                'message' => 'Ride_not_found'
            ];

        if ($ride->status == 'Canceled')
            return [
                'status' => false,
                'message' => 'Ride_Canceled'
            ];
        else if ($ride->status == 'Completed')
            return [
                'status' => false,
                'message' => 'Ride_Already_Completed'
            ];
        else {
            if ($ride->status != 'Waiting_Customer')
                return [
                    'status' => false,
                    'message' => 'Ride_Status_is_Not_Waiting_Customer'
                ];
            $ride->status = 'Started';
            date_default_timezone_set('Asia/Baku');
            $ride->waitingEnd = date('Y-m-d H:i:s');
            $ride->save();
            return [
                'status' => true,
                'message' => 'Ride_Started'
            ];
        }
    }
    // http://rahatgetayigdriverapi.test/api/driver/EndRideAyig 

    public function CreateRideAyig(Request $req)
    {
        $req->validate([
            'takeLocation' => 'required',
            'startLocationName' => 'required',
            'startDate' => 'required',
            'userId' => 'required',
            'endLocationName' => 'required',
            'endLocation' => 'required',
        ]);

        $user = $req->user();
        // $ride=ayigRide::Create([
        //     ''
        // ])
        $rideUser = new rideAyigForUser();
        $rideUser->status = 'Pending';
        $rideUser->takeLocation = $req->takeLocation;
        $rideUser->startLocationName = $req->startLocationName;
        $rideUser->startDate = $req->startDate;
        $rideUser->endLocation = $req->endLocation;
        $rideUser->endLocationName = $req->endLocationName;
        $rideUser->userId = $req->userId;
        $rideUser->AyigDriverId = $user->id;

        $rideUser->save();

        return response()->json([
            'status' => true,
            'message' => 'Ride_Created_Successfully',
            'rideId' => $rideUser->id
        ], 200);
    }
}
