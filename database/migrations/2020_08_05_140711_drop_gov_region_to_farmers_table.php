<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropGovRegionToFarmersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('farmers', function (Blueprint $table) {
            
            $table->dropForeign('farmers_governerate_code_foreign');
            $table->dropForeign('farmers_region_code_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('farmers', function (Blueprint $table) {
            //
        });
    }

}
