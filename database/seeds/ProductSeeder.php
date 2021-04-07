<?php

use App\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected $products = [
        // for = 1 is local market
        ['name' => 'GRADE1 HUSK', 'container_code' => 'HS', 'for' => 1],
        ['name' => 'GRADE2 HUSK', 'container_code' => 'QS', 'for' => 1],
        ['name' => 'GRADE3 HUSK', 'container_code' => 'KS', 'for' => 1],
        ['name' => 'ELEPHANT BEANS', 'container_code' => 'SS', 'for' => 1],
        ['name' => 'SMALL BEANS', 'container_code' => 'SS', 'for' => 1],

        // for = 2 is coffee sorting
        ['name' => 'SIZE1 GREEN COFFEE', 'container_code' => 'GSA', 'for' => 2],
        ['name' => 'SIZE2 GREEN COFFEE', 'container_code' => 'GSB', 'for' => 2],
        ['name' => 'PEABERRY', 'container_code' => 'PS', 'for' => 2],

        // for = 3 is export after sorting
        ['name' => 'PEABERRY WITHOUT DEFECTS', 'container_code' => 'PS', 'for' => 3],
        ['name' => 'SIZE 1 GREEN COFFEE WITHOUT DEFECTS', 'container_code' => 'ESA', 'for' => 3],
        ['name' => 'SIZE 2 GREEN COFFEE WITHOUT DEFECTS', 'container_code' => 'ESB', 'for' => 3],

        // for = 4 is defective for local market sales
        ['name' => 'PEABERRY DEFECTS', 'container_code' => 'LS', 'for' => 4],
        ['name' => 'SIZE 1 DEFECT GREEN COFFEE', 'container_code' => 'LS', 'for' => 4],
        ['name' => 'SIZE 2 DEFECT GREEN COFFEE', 'container_code' => 'LS', 'for' => 4],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::truncate();

        foreach ($this->products as $product) {
            Product::create([
                'name' => $product['name'],
                'container_code' => $product['container_code'],
                'for' => $product['for']
            ]);
        }
    }
}
