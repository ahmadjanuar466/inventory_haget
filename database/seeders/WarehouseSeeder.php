<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $row = [
            'branch_id' => '1',
            'code' => 'HGT-STORE-1',
            'name' => 'Store Aster Village',
            'is_active' => '1'
        ];
        Warehouse::create($row);
    }
}
