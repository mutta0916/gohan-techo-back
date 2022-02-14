<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMenuRecipesColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_recipes', function (Blueprint $table) {
            $table->dropForeign('menu_recipes_user_id_foreign');
            $table->dropPrimary();
            $table->dropColumn('user_id');
        });

        Schema::table('menu_recipes', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_recipes', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->unsignedBigInteger('user_id')->first();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->primary(['user_id', 'menu_id', 'location']);
        });
    }
}
