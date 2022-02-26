<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeHowtosColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipe_howtos', function (Blueprint $table) {
            $table->dropForeign('recipe_howtos_user_id_foreign');
            $table->dropPrimary();
            $table->dropColumn('user_id');
        });

        Schema::table('recipe_howtos', function (Blueprint $table) {
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
        Schema::table('recipe_howtos', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->unsignedBigInteger('user_id')->first();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->primary(['user_id', 'recipe_id']);
        });
    }
}
