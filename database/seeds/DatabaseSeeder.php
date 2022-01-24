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
        DB::table('users')->truncate();
        DB::table('recipe_genres')->truncate();
        DB::table('recipe_types')->truncate();

        $this->call([
            UsersTableSeeder::class,
            GenreTableSeeder::class,
            TypeTableSeeder::class,
        ]);
    }
}
