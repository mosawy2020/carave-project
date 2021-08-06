<?php

namespace App;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class docsignmodel extends Authenticatable implements JWTSubject

{
    use Notifiable;
    protected $table = 'doctor' ; 
    protected $fillable = [
        'name', 'email', 'password','webtoken','mobiletoken'
        ,'image'
        ,'holidaydays','adress','mobile','cost','nationalid','starttime','endtime','cv','raters'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getAuthPassword() {
        return $this->password;
    }  
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
