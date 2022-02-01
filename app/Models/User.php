<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Carbon;


class User extends Authenticatable implements JWTSubject
{
    //public $timestamps = false;
    protected $dateFormat = 'U';


    use Notifiable;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','api_token',
    ];
   


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
       
    ];

    public function pictures()
    {
        return $this->hasMany('App\Models\Post');
    }
    
    public function comments(){
        return $this->hasMany('App\Models\Comment');
    }

    public function getJWTIdentifier(){ return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }
    /*public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->format('d-m-Y');
    }

    public function getUpdatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->format('d-m-Y');
    }*/
   
}
