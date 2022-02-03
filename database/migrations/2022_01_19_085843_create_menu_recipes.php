<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuRecipes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_recipes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedBigInteger('menu_id')->references('id')->on('menus')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->tinyInteger('location');
            $table->unsignedBigInteger('recipe_id')->references('id')->on('recipes')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_recipes');
    }
}
