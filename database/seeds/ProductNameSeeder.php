<?php

use App\ProductName;
use Illuminate\Database\Seeder;

class ProductNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productNames = [
            'GRADE1 HUSK',
            'GRADE2 HUSK',
            'GRADE3 HUSK',
            'ELEPHANT BEANS',
            'SMALL BEANS',
            'SIZE1 GREEN COFFEE',
            'SIZE 2 GREEN COFFEE',
            'PEABERRY'
        ];

        foreach ($productNames as $name) {
            ProductName::create(['name' => $name]);
        }
    }
}
