<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Comment;
use Faker\Generator as Faker;
use Illuminate\Support\Str;


$factory->define(Comment::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'post_id' => 1,
        'comment' => Str::random(10),
        
        'created_at' =>  now(),
        'updated_at' => now(),
    ];
});
