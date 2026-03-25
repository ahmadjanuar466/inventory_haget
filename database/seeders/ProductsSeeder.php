<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $row = [
            [
                'sku' => 'RM-001',
                'name' => 'Susu Ultra',
                'category_id' => '1',
                'units_id' => '3',
                'has_expiry' => 1,
                'cost_price' => '19000',
                'sell_price' => null,
                'min_stock' => 10
            ],
            [
                'sku' => 'FG-001',
                'name' => 'Susu Kurma Original',
                'category_id' => '2',
                'units_id' => '6',
                'has_expiry' => 1,
                'cost_price' => '19000',
                'sell_price' => null,
                'min_stock' => 10
            ]
        ];
        foreach ($row as $key => $value) {
            Products::create($value);
        }
    }
}
