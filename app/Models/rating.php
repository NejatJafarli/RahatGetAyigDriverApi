<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rating extends Model
{
    use HasFactory;
    public $connection = 'mysql';
    public $table = 'ratings';
    protected $fillable = [
        'driverId',
        'userId',
        'startCount',
        'comment'
    ];
}
