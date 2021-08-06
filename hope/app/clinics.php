<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clinics extends Model
{
    protected $table = 'clinics' ; 
    protected $fillable = [
        'day', 'mobile', 'address','docid','srarttime','endtime'
 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

}
