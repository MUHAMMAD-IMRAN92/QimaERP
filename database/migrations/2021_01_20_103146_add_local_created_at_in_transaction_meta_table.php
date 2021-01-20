<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocalCreatedAtInTransactionMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_meta', function (Blueprint $table) {
            $table->dateTime('local_created_at')->nullable()->useCurrent()->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_meta', function (Blueprint $table) {
            $table->dropColumn('local_created_at');
        });
    }
}
