<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;

class Post extends Model
{  

    protected $casts = [
        
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
       
    ];


    public function users(){
        return $this->belongsTo('App\Models\User');
    }

     /**
     * @param $customer_id
     * @return string
     */
    public function checkOwner($id)
    {
        if ($this->user_id !== $id) {
            abort(403);
        }
    }

}
