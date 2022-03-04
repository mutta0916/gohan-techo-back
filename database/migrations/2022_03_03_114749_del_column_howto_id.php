<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DelColumnHowtoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipe_howtos', function (Blueprint $table) {
            $table->dropColumn('howto_id');
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
            $table->tinyInteger('howto_id')->after('recipe_id');
        });
    }
}
