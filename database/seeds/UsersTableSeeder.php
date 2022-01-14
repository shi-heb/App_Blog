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
            ["id"=>91,
            "name"=>'user1',
            "password"=>"azeryty123",
            "created_at"=>$date,
            "updated_at"=>$date,
            "email"=>"email1@live31.fr"

        ],
        ];

        DB::table('users')->insert($data);
    }
}
