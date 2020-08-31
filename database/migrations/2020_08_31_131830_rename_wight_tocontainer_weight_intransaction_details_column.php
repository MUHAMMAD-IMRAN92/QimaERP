<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameWightTocontainerWeightIntransactionDetailsColumn extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::table('transactions', function(Blueprint $table) {
            $table->boolean('is_new')->default(0)->nullable();
            $table->integer('sent_to')->nullable();
        });
        Schema::table('transaction_details', function(Blueprint $table) {
            $table->renameColumn('weight', 'container_weight');
            $table->string('weight_unit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }

}
