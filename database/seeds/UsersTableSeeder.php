<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = now();
        $data = [
            ["id"=>204,
            "name"=>'useradmin',
            "password"=>bcrypt("24089974"),
            "created_at"=>$date,
            "updated_at"=>$date,
            "email"=>"adminapp@live.fr",
            "role"=>"admin"

        ],
        ];

        DB::table('users')->insert($data);
    }
}
