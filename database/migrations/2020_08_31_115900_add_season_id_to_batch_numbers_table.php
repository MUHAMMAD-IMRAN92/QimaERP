<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeasonIdToBatchNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batch_numbers', function (Blueprint $table) {
            $table->bigInteger('season_id')->unsigned()->nullable();
            $table->foreign('season_id')->references('season_id')->on('seasons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batch_numbers', function (Blueprint $table) {
            //
        });
    }
}
