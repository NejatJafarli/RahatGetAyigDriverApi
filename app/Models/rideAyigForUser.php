<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rideAyigForUser extends Model
{
    use HasFactory;
    public $connection = 'mysql2';

    public $table = 'ride_ayigs';
    protected $fillable = [
        'status',
        'takeLocation',
        'startLocationName',
        'startDate',
        'endLocation',
        'endLocationName',
        'endDate',
        'price',
        'userId',
        'AyigDriverId',
        'OrderId',
        'waitingStart',
        'waitingEnd'
    ];
}
