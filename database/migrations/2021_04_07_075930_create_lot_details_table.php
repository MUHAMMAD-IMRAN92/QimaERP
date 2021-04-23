<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLotDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id');
            $table->foreignId('product_id');
            $table->string('container_number');
            $table->decimal('weight');
            $table->string('weight_unit');
            $table->tinyInteger('status')->default(1)->comment('1= available in inventory 0= sold');
            $table->foreignId('reference_id')->nullable()->comment('it reffers to the id of same table from which this is creted or sorted.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lot_details');
    }
}
