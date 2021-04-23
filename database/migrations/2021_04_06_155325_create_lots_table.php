<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number')->comment('auto generated log number');
            $table->foreignId('transaction_id')->comment('fk of transanction');
            $table->foreignId('user_id')->comment('fk of user');
            $table->boolean('is_mixed')->default(false)->comment('0 or 1 if this is a bixed batch');
            $table->tinyInteger('transaction_type')->comment('1=in, 2=out, 3=incoming, 4=outgoing');
            $table->string('refference_ids')->comment('these are the one or more ids of transactions from which this is made.');
            $table->integer('sent_to')->nullable();
            $table->boolean('is_in_process')->default(false);
            $table->unsignedBigInteger('session_no');
            $table->boolean('is_sent')->default(false);
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
        Schema::dropIfExists('lots');
    }
}
