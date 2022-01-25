<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRecipeHowtosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipe_howtos', function (Blueprint $table) {
            $table->primary(['user_id', 'recipe_id', 'howto_id']);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('howto')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipe_howtos', function (Blueprint $table) {
            $table->dropForeign('recipe_howtos_user_id_foreign');
            $table->dropForeign('recipe_howtos_recipe_id_foreign');
            $table->dropPrimary(['user_id', 'menu_id', 'location']);
            $table->string('howto')->nullable(false)->change();
        });
    }
}
