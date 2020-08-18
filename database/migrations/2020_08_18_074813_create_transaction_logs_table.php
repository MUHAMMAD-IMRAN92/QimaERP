<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id('transaction_log_id');
            $table->bigInteger('transaction_id')->unsigned()->nullable();
            $table->foreign('transaction_id')->references('transaction_id')->on('transactions')->onDelete('cascade');
            $table->string('action')->comment('Created, sent, received');
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->integer('sent_to');
            $table->dateTime('local_created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('transaction_logs');
    }

}
