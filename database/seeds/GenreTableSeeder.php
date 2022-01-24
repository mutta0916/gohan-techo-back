<?php

use Illuminate\Database\Seeder;

class GenreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'genre' => '和食'
        ];
        DB::table('recipe_genres')->insert($param);

        $param = [
            'genre' => '洋食'
        ];
        DB::table('recipe_genres')->insert($param);

        $param = [
            'genre' => '中華'
        ];
        DB::table('recipe_genres')->insert($param);

        $param = [
            'genre' => 'その他'
        ];
        DB::table('recipe_genres')->insert($param);
    }
}
