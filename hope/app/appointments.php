<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class appointments extends Model
{

    protected $table = 'appointments' ; 
    protected $fillable = [
         'patientid', 'date','cost','docid','clinicid'
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
