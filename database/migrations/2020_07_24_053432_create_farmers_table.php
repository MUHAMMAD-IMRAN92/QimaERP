<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('farmers', function (Blueprint $table) {
            $table->id('farmer_id');
            $table->string('farmer_code')->index();
            $table->string('farmer_name');
            $table->string('governerate_code', 10);
            $table->foreign('governerate_code')->references('governerate_code')->on('governerates');

            $table->string('region_code', 10);
            $table->foreign('region_code')->references('region_code')->on('regions');

            $table->string('village_code');
            $table->foreign('village_code')->references('village_code')->on('villages');


            $table->bigInteger('picture_id')->unsigned()->nullable();
            $table->foreign('picture_id')->references('file_id')->on('file_systems')->onDelete('cascade');

            $table->bigInteger('idcard_picture_id')->unsigned()->nullable();
            $table->foreign('idcard_picture_id')->references('file_id')->on('file_systems')->onDelete('cascade');

            $table->boolean('is_status')->default(0)->comment('0=not approved ,1=approved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('farmers');
    }

}
