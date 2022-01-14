<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function users(){
        return $this->belongsTo('App\Models\User');
    }
    public function pictures(){
        return $this->belongsTo('App\Models\Post');
    }
}
