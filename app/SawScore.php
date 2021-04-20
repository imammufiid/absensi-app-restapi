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
        'user_id', 'citeria_id', 'point'
    ];
}
