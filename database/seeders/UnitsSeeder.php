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
        $row = [
            ['code' => 'PCS', 'name' => 'Pcs'],
            ['code' => 'KG', 'name' => 'Kilogram'],
            ['code' => 'LTR', 'name' => 'Liter'],
            ['code' => 'PACK', 'name' => 'Pack'],
            ['code' => 'BOX', 'name' => 'Box'],
            ['code' => 'MLTR', 'name' => 'Mili Liter']
        ];
        foreach ($row as $key => $value) {
            Units::create($value);
        }
    }
}
