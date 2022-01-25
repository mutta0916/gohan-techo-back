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
            'genre' => '未指定'
        ];
        DB::table('recipe_genres')->insert($param);

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
    }
}
