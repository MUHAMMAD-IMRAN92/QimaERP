<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToFarmers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->string('ph_no')->nullable();
            $table->string('reward')->nullable();
            $table->string('cup_profile')->nullable();
            $table->string('cupping_score')->nullable();
            $table->string('farmer_info')->nullable();
            $table->integer('no_of_trees')->nullable();
            $table->integer('house_hold_size')->nullable();
            $table->string('farm_size')->nullable();
            $table->string('altitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->dropColumn(
                'ph_no',
                'reward',
                'cup_profile',
                'cupping_score',
                'farmer_info',
                'no_of_trees',
                'house_hold_size',
                'farm_size',
                'altitude'
            );
        });
    }
}
