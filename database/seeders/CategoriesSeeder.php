<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Categories::truncate();
        $row = [
            ['parent_id' => null, 'code' => 'CAT-RAW', 'name' => 'Bahan Baku'],
            ['parent_id' => null, 'code' => 'CAT-FG', 'name' => 'Barang Jadi'],
            ['parent_id' => null, 'code' => 'CAT-PKG', 'name' => 'Packaging']
        ];
        foreach ($row as $key => $value) {
            Categories::create($value);
        }
    }
}
