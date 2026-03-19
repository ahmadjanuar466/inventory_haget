<?php

namespace Database\Seeders;

use App\Models\Units;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Units::truncate();
        $row = [
            ['code' => 'PCS', 'name' => 'Pcs'],
            ['code' => 'KG', 'name' => 'Kilogram'],
            ['code' => 'LTR', 'name' => 'Liter'],
            ['code' => 'PACK', 'name' => 'Pack'],
            ['code' => 'BOX', 'name' => 'Box'],
        ];
    }
}
