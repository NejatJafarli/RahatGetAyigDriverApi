<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Driver extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname',
        'phone',
        'age',
        'password',
        'photo',
        'email',
        'status',
        'balance',
        'want_reservation',
        'fin_code',
        'license_number',
        'online'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}