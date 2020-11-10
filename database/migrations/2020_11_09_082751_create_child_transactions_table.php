<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildTransactionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('child_transactions', function (Blueprint $table) {
            $table->id('child_transactions_id');
            $table->bigInteger('parent_transaction_id')->unsigned()->nullable();
            $table->foreign('parent_transaction_id')->references('transaction_id')->on('transactions')->onDelete('cascade');

            $table->bigInteger('transaction_id')->unsigned()->nullable();
            $table->string('batch_number', 20);
            $table->bigInteger('is_parent')->default(0);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->boolean('is_local')->default(0)->nullable();
            $table->string('local_code')->nullable();
            $table->boolean('is_mixed')->default(0)->nullable();
            $table->integer('transaction_type')->default(1)->comment('1=in, 2=out');
            $table->string('transaction_status')->comment('Created, sent, received');
            $table->boolean('is_new')->default(0)->nullable();
            $table->integer('sent_to')->nullable();
            $table->boolean('is_server_id')->default(0)->nullable();
            $table->boolean('is_sent')->default(0);
            $table->string('reference_id')->nullable();
            $table->integer('session_no')->default(1)->nullable();
            $table->dateTime('local_created_at')->nullable();
            $table->boolean('is_in_process')->default(0)->nullable();
            $table->boolean('is_update_center')->default(0)->nullable();
            $table->integer('local_session_no')->default(1)->nullable();
            $table->integer('mill_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('child_transactions');
    }

}
