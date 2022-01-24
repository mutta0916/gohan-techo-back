<?php

use Illuminate\Database\Seeder;

class TypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'type' => '主食'
        ];
        DB::table('recipe_types')->insert($param);

        $param = [
            'type' => '主菜'
        ];
        DB::table('recipe_types')->insert($param);

        $param = [
            'type' => '副菜'
        ];
        DB::table('recipe_types')->insert($param);

        $param = [
            'type' => '汁物'
        ];
        DB::table('recipe_types')->insert($param);

        $param = [
            'type' => '丼'
        ];
        DB::table('recipe_types')->insert($param);

        $param = [
            'type' => '麺'
        ];
        DB::table('recipe_types')->insert($param);

        $param = [
            'type' => 'その他'
        ];
        DB::table('recipe_types')->insert($param);
    }
}
