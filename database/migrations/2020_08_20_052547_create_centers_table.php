<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('centers', function (Blueprint $table) {
            $table->id('center_id');
            $table->string('center_code', 20)->index();
            $table->string('center_name');
            $table->bigInteger('center_manager_id')->unsigned()->nullable();
            $table->foreign('center_manager_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('centers');
    }

}
