<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    public function users(){
        return $this->belongsTo('App\Models\User');
    }
    public function posts(){
        return $this->belongsTo('App\Models\Post');
    }
}
