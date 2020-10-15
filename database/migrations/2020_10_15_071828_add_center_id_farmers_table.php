<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCenterIdFarmersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('farmers', function (Blueprint $table) {
            $table->bigInteger('center_id')->unsigned()->nullable();
            $table->foreign('center_id')->references('center_id')->on('centers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('farmers', function (Blueprint $table) {
            //
        });
    }

}
