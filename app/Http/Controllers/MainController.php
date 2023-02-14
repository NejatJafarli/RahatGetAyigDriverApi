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
                'message' => 'Rides not found'
            ]);
        else
            return response()->json([
                'status' => true,
                'message' => 'Rides found',
                'data' => $rides
            ]);
    }
    public function EndUserAyigRide(Request $req)
    {
        $req->validate([
            'rideId' => 'required',
        ]);
        //get rideAyigForUser for rideId
        $ride = rideAyigForUser::find($req->rideId);
        if ($ride == null)
            return [
                'status' => false,
                'message' => 'Ride not found'
            ];
        if ($ride->status == 'Canceled')
            return [
                'status' => false,
                'message' => 'Ride Canceled'
            ];
        else {
            if ($ride->AyigDriverId != $req->user()->id)
                return [
                    'status' => false,
                    'message' => 'You are not the driver of this ride'
                ];
            $ride->status = 'Completed';
            $ride->save();
            return [
                'status' => true,
                'message' => 'Ride finished'
            ];
        }
    }
    //create ride Ayig
    public function InfoDriverAyigRide(Request $req)
    {
        $req->validate([
            'rideId' => 'required',
        ]);
        //get rideAyigForUser for rideId
        $ride = rideAyigForUser::find($req->rideId);
        if ($ride == null)
            return [
                'status' => false,
                'message' => 'Ride not found'
            ];
        else
            return [
                'status' => true,
                'message' => 'Ride found',
                'data' => $ride
            ];
    }

    public function AcceptRideAyig(Request $req)
    {
        $req->validate([
            'rideId' => 'required',
            'OrderId' => 'required'
        ]);

        $ride = rideAyigForUser::find($req->rideId);

        if ($ride == null)
            return [
                'status' => false,
                'message' => 'Ride not found'
            ];

        if ($ride->status == 'Canceled')
            return [
                'status' => false,
                'message' => 'Ride Canceled'
            ];
        else if ($ride->status == 'Accepted')
            return [
                'status' => false,
                'message' => 'Ride Already Accepted'
            ];
        else if ($ride->status == 'Completed')
            return [
                'status' => false,
                'message' => 'Ride Already Completed'
            ];
        else {
            if($ride->status!='rezerv')
            {
                if ($ride->status != 'Pending')
                return [
                    'status' => false,
                    'message' => 'Ride Not Pending'
                ];
            }
            $ride->status = 'Accepted';
            $ride->AyigDriverId = $req->user()->id;
            $ride->OrderId = $req->OrderId;
            $ride->save();
            return [
                'status' => true,
                'message' => 'Ride Accepted'
            ];
        }
    }
    public function DriverIsOnline(Request $req){
        $req->validate([
            'IsOnline' => 'required'
        ]);
        $driver = $req->user();
        $driver->online = $req->IsOnline;
        $driver->save();
        return [
            'status' => true,
            'message' => 'Driver is online or offline'
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
                'message' => 'this User is not exist',
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
            'message' => 'User found',
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
                'message' => 'Ride not found'
            ];

        if ($ride->status == 'Canceled')
            return [
                'status' => false,
                'message' => 'Ride Canceled'
            ];
        else if ($ride->status == 'Completed')
            return [
                'status' => false,
                'message' => 'Ride Already Completed'
            ];
        else {
            if ($ride->status != 'Accepted')
                return [
                    'status' => false,
                    'message' => 'Ride Not Accepted'
                ];
            $ride->status = 'Waiting Customer';
            $ride->waitingStart = date('Y-m-d H:i:s');
            $ride->save();
            return [
                'status' => true,
                'message' => 'Ride Waiting Customer'
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
                'message' => 'Ride not found'
            ];

        if ($ride->status == 'Canceled')
            return [
                'status' => false,
                'message' => 'Ride Canceled'
            ];
        else if ($ride->status == 'Completed')
            return [
                'status' => false,
                'message' => 'Ride Already Completed'
            ];
        else {
            if ($ride->status != 'Waiting Customer')
                return [
                    'status' => false,
                    'message' => 'Ride Not Waiting Customer'
                ];
            $ride->status = 'Started';
            $ride->waitingEnd = date('Y-m-d H:i:s');
            $ride->save();
            return [
                'status' => true,
                'message' => 'Ride Started'
            ];
        }
    }
    public function StartAyigRide(Request $req)
    {
        $req->validate([
            'rideId' => 'required',
        ]);

        $ride = rideAyigForUser::find($req->rideId);

        if ($ride == null)
            return [
                'status' => false,
                'message' => 'Ride not found'
            ];

        if ($ride->status == 'Canceled')
            return [
                'status' => false,
                'message' => 'Ride Canceled'
            ];
        else if ($ride->status == 'Completed')
            return [
                'status' => false,
                'message' => 'Ride Already Completed'
            ];
        else {
            if ($ride->status != 'Accepted')
                return [
                    'status' => false,
                    'message' => 'Ride Not Accepted'
                ];
            $ride->status = 'Started';
            $ride->waitingEnd = date('Y-m-d H:i:s');
            $ride->save();
            return [
                'status' => true,
                'message' => 'Ride Waiting Customer'
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
            'message' => 'Ride Created Successfully',
            'rideId' => $rideUser->id
        ], 200);
    }
}
