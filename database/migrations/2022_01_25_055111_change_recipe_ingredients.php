<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRecipeIngredients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->primary(['user_id', 'recipe_id', 'ingredient_id']);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('name')->nullable(true)->change();
            $table->string('amount')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->dropForeign('recipe_ingredients_user_id_foreign');
            $table->dropForeign('recipe_ingredients_recipe_id_foreign');
            $table->dropPrimary(['user_id', 'recipe_id', 'ingredient_id']);
            $table->string('name')->nullable(false)->change();
            $table->string('amount')->nullable(false)->change();
        });
    }
}
