<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class records extends Model
{
    protected $table = 'records' ; 
    protected $fillable = [
        'name', 'result', 'patientid' , 'resultasimage'
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
