<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('users')->truncate();
        DB::table('recipes')->truncate();
        DB::table('recipe_genres')->truncate();
        DB::table('recipe_types')->truncate();
        DB::table('recipe_ingredients')->truncate();
        DB::table('recipe_howtos')->truncate();
        DB::table('menus')->truncate();
        DB::table('menu_recipes')->truncate();

        $this->call([
            UsersTableSeeder::class,
            GenreTableSeeder::class,
            TypeTableSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
