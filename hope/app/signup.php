<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class signup extends Model
{
    protected $table = 'patient' ; 
    protected $fillable = [
        'name', 'email', 'password','webtoken','mobiletoken'
        ,'image'
        ,'bloodtype','adress','mobile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
