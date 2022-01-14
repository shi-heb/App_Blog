<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Post;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;


$factory->define(Post::class, function (Faker $faker) {
    return [
        
        'user_id' => 1,
        'source' => Str::random(10),
        'title' =>  Str::random(10),
        'description'=>Str::random(10),
        'created_at' =>  now(),
        'updated_at' => now(),
    ];
});
