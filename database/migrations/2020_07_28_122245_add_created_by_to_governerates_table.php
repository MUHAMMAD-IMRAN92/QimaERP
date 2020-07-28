<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByToGoverneratesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('governerates', function (Blueprint $table) {
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->boolean('is_local')->default(1)->nullable();
            $table->string('local_code')->nullable();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->boolean('is_local')->default(1)->nullable();
            $table->string('local_code')->nullable();
        });
        Schema::table('villages', function (Blueprint $table) {
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->boolean('is_local')->default(1)->nullable();
            $table->string('local_code')->nullable();
        });
        Schema::table('farmers', function (Blueprint $table) {
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->boolean('is_local')->default(1)->nullable();
            $table->string('local_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('governerates', function (Blueprint $table) {
            //
        });
    }

}
