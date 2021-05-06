<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'date', 'time_comes', 'time_gohome', 'attendance_type', 'information', 'file_information'
    ];
}
