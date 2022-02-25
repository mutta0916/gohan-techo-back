<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipeHowtosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipe_howtos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedBigInteger('recipe_id')->references('id')->on('recipes')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->tinyInteger('howto_id');
            $table->string('howto');
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
        Schema::dropIfExists('recipe_howtos');
    }
}
