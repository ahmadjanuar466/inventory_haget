<?php

namespace Database\Seeders;

use App\Models\Branches;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Branches::truncate();
        $row = [
            'code' => 'HGT001',
            'name' => 'Haget Store 1',
            'branch_type_id' => '3',
            'address' => 'Aster Village Blok E33',
            'phone' => '087821910608'
        ];
        Branches::insert($row);
    }
}
