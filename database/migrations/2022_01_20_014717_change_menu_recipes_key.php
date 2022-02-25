<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMenuRecipesKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_recipes', function (Blueprint $table) {
            $table->primary(['user_id', 'menu_id', 'location']);
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
            $table->dropForeign('menu_recipes_user_id_foreign');
            $table->dropForeign('menu_recipes_menu_id_foreign');
            $table->dropPrimary(['user_id', 'menu_id', 'location']);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('menu_id')->references('id')->on('menus')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }
}
