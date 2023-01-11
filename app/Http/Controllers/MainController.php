<?php

namespace App\Http\Controllers;

use App\Models\ayigRide;
use App\Models\rideAyigForUser;
use Illuminate\Http\Request;

class MainController extends Controller
{
    //
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
    public function CheckUserAyigRide(Request $req)
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
        else
            return [
                'status' => true,
                'message' => 'Ride is not Canceled'
            ];
    }

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
