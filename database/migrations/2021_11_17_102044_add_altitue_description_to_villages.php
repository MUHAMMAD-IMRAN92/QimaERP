<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAltitueDescriptionToVillages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->integer('altitude')->nullable()->after('attach_images');
            $table->float('reward_per_kg')->nullable()->after('altitude');
            $table->string('description')->nullable()->after('reward_per_kg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropColumn('altitude', 'reward_per_kg', 'description');
        });
    }
}
