<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('containers', function (Blueprint $table) {
            $table->id('container_id');
            $table->string('container_number')->index();
            $table->integer('container_type')->default(0)
                    ->comment('1=Basket,2=Drying Tables,3=Special Process barrel,4=Drying Machine (Future),5=Dry Coffee Bag,6=Pre Defect removal Export Coffee (Size 1 and Size 2) bag,7=Defect Free Export coffee (Size 1 and Size 2) bag,8=Peaberry Coffee Bag,9=Grade 2 Coffee (small and big beans),10=Grade 3 (defect) Coffee,11=Grade 1 husk  Bag ,12=Grade 2 husk Bag,13=Grade 3 husk bag ,14=5kg Vacuum Bag for export,15=15kg Premium Bag for export,16=10kg Shipping Box,17=30kg Shipping Box,18=Sample Bag 1');
            $table->decimal('capacity', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('containers');
    }

}
