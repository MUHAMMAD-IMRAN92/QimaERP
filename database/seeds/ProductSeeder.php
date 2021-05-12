<?php

use App\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected $products = [
        // for = 1 is local market
        ['name' => 'GRADE 1 HUSK', 'container_code' => 'HS', 'for' => 1],
        ['name' => 'GRADE 2 HUSK', 'container_code' => 'QS', 'for' => 1],
        ['name' => 'GRADE 3 HUSK', 'container_code' => 'KS', 'for' => 1],
        ['name' => 'ELEPHANT BEANS', 'container_code' => 'SS', 'for' => 1],
        ['name' => 'SMALL BEANS', 'container_code' => 'SS', 'for' => 1],

        // for = 2 is coffee sorting
        ['name' => 'SIZE 1 GREEN COFFEE', 'container_code' => 'GSA', 'for' => 2],
        ['name' => 'SIZE 2 GREEN COFFEE', 'container_code' => 'GSB', 'for' => 2],
        ['name' => 'PEABERRY', 'container_code' => 'PS', 'for' => 2],

        // for = 3 
        ['name' => 'PEABERRY WITHOUT DEFECTS', 'container_code' => 'PS', 'for' => 3],

        // for = 4 
        ['name' => 'SIZE 1 GREEN COFFEE WITHOUT DEFECTS', 'container_code' => 'ESA', 'for' => 4],
        ['name' => 'SIZE 2 GREEN COFFEE WITHOUT DEFECTS', 'container_code' => 'ESB', 'for' => 4],

        // for = 5 is defective for local market sales
        ['name' => 'PEABERRY DEFECTS', 'container_code' => 'LS', 'for' => 5],
        ['name' => 'SIZE 1 DEFECT GREEN COFFEE', 'container_code' => 'LS', 'for' => 6],
        ['name' => 'SIZE 2 DEFECT GREEN COFFEE', 'container_code' => 'LS', 'for' => 6],

        // for = 6 is all defective mix in local market sales
        ['name' => 'DEFECTS GREEN COFFEE', 'container_code' => 'LS', 'for' => 6],
        ['name' => 'GRADE 2 GREEN COFFEE', 'container_code' => 'SS', 'for' => 7],
        ['name' => 'GRADE 3 GREEN COFFEE', 'container_code' => 'LS', 'for' => 7],
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
