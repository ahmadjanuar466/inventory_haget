<?php

namespace Database\Seeders;

use App\Models\BranchType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BranchType::truncate()
        //
        $row=[
            ['name'=>'store'],
            ['name'=> 'warehouse'],
            ['name'=> 'head office'],
        ];
        foreach ($row as $key => $value) {
            BranchType::create($value);
        }


    }
}
