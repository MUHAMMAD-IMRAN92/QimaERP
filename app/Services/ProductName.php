<?php

namespace App\Services;

use Illuminate\Support\Arr;

class ProductName
{
    private const PRODUCTS = [
        [
            'id' => 1,
            'name' => 'GRADE1 HUSK',
            'container_code' => 'HS'
        ],
        [
            'id' => 2,
            'name' => 'GRADE2 HUSK',
            'container_code' => 'QS'
        ],
        [
            'id' => 3,
            'name' => 'GRADE3 HUSK',
            'container_code' => 'KS'
        ],
        [
            'id' => 4,
            'name' => 'ELEPHANT BEANS',
            'container_code' => 'SS'
        ],
        [
            'id' => 5,
            'name' => 'SMALL BEANS',
            'container_code' => 'SS'
        ],
        [
            'id' => 6,
            'name' => 'SIZE1 GREEN COFFEE',
            'container_code' => 'GSA'
        ],
        [
            'id' => 7,
            'name' => 'SIZE2 GREEN COFFEE',
            'container_code' => 'GSB'
        ],
        [
            'id' => 8,
            'name' => 'PEABERRY',
            'container_code' => 'PS'
        ],
    ];

    public static function all(){
        return collect(self::PRODUCTS)->map(function($product){
            return (object) $product;
        });
    }
}
