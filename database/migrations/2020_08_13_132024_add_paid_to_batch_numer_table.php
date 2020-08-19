<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidToBatchNumerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batch_numbers', function (Blueprint $table) {
                 $table->boolean('is_mixed')->default(0)->nullable();
        });
        Schema::table('transactions', function (Blueprint $table) {
               $table->boolean('is_mixed')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batch_numer', function (Blueprint $table) {
            //
        });
    }
}
