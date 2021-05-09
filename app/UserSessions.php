<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSessions extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'is_login', 'detail_login', 'email'
    ];
}
