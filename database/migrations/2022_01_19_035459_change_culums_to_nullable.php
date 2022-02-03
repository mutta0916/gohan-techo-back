<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCulumsToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipes', function (Blueprint $table) {
            DB::statement('alter table recipes modify column genre_id tinyint null');
            DB::statement('alter table recipes modify column type_id tinyint null');
            DB::statement('alter table recipes modify column servings tinyint null');
            $table->string('photo',100)->nullable(true)->change();
            $table->string('memo',100)->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipes', function (Blueprint $table) {
            DB::statement('alter table recipes modify column genre_id tinyint not null');
            DB::statement('alter table recipes modify column type_id tinyint not null');
            DB::statement('alter table recipes modify column servings tinyint not null');
            $table->string('photo',100)->nullable(false)->change();
            $table->string('memo',100)->nullable(false)->change();
        });
    }
}
