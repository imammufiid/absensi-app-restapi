<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCriteria extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'point', 'criteria_id'
    ];
}
