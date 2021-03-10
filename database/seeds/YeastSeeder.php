<?php

use App\Yeast;
use Illuminate\Database\Seeder;

class YeastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $yeasts = [
            ['en' => 'sodium carbonate', 'ar' => 'كربونات الصوديوم'],
            ['en' => 'sodium nitrite', 'ar' => 'نترات الصوديوم'],
            ['en' => 'monosodium phosphate', 'ar' => 'فوسفات أحادي الصوديوم'],
            ['en' => 'disodium phosphate', 'ar' => 'فوسفات ثنائي الصوديوم']
        ];

        foreach ($yeasts as $yeast) {
            Yeast::create([
                'yeast_name' => $yeast['en'],
                'ar_yeast_name' => $yeast['ar']
            ]);
        }
    }
}
