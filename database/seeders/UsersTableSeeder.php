<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create();
        $limit = 20;

        for ($i = 0; $i < $limit; $i++){
        DB::table('users')->insert([
            'name' => $faker -> name,
            'email' => $faker -> unique()-> email,
            'password' =>bcrypt($faker-> password ), 
            'number_phone' => $faker-> phoneNumber,
                

        ]);
    }
}
}
