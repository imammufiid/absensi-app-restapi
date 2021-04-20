<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SawScore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'criteria_id', 'point', 'date'
    ];

    protected $casts = [
        'date' => 'datetime:d-m-Y', // Change your format
    ];
}
