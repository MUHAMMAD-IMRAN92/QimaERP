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
            ['name' => 'GRADE1 HUSK', 'container_code' => 'HS'],
            ['name' => 'GRADE2 HUSK', 'container_code' => 'QS'],
            ['name' => 'GRADE3 HUSK', 'container_code' => 'KS'],
            ['name' => 'ELEPHANT BEANS', 'container_code' => 'SS'],
            ['name' => 'SMALL BEANS', 'container_code' => 'SS'],
            ['name' => 'SIZE1 GREEN COFFEE', 'container_code' => 'GSA'],
            ['name' => 'SIZE2 GREEN COFFEE', 'container_code' => 'GSB'],
            ['name' => 'PEABERRY', 'container_code' => 'PS'],
        ];

        ProductName::truncate();

        foreach ($productNames as $product) {
            ProductName::create([
                'name' => $product['name'],
                'container_code' => $product['container_code']
            ]);
        }
    }
}
