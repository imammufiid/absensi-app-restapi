<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attendance_id', 'latitude', 'longitude', 'lbs'
    ];
}
