<?php

namespace App\Models;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\DB;



use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Carbon;


class User extends Authenticatable implements JWTSubject
{
    //public $timestamps = false;
   


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

    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }
    
   public function comments(){
       return $this->hasMany('App\Models\Comment');
      // return $this->hasManyThrough(Comment::class, Post::class);
    }

    public function getJWTIdentifier(){ return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }

    /*public function comments()
    {
        return $this->hasManyThrough(
            Comment::class,
            Post::class,
            'user_id', // Foreign key on the environments table...
            'post_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );
    }*/

 
   

    
   
}


