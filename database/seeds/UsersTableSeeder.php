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
        $param = [
          'name' => 'h.satomi',
          'email' => Str::random(10).'@gmail.com',
          'password' => Hash::make('password'),
        ];
        DB::table('users')->insert($param);
    }
}
