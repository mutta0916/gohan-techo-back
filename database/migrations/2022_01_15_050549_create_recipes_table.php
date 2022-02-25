<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('name',40);
            $table->tinyInteger('genre_id')->references('id')->on('recipe_genres')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->tinyInteger('type_id')->references('id')->on('recipe_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->tinyInteger('servings');
            $table->string('photo',100);
            $table->string('memo',100);
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
        Schema::dropIfExists('recipes');
    }
}
