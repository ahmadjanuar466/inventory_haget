<?php

namespace Database\Seeders;

use App\Models\CustomerType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $row = [
            ['name' => 'regular'],
            ['name' => 'regular'],
            ['name' => 'member'],
            ['name' => 'reseller']
        ];
        foreach ($row as $key => $value) {
            CustomerType::create($value);
        }
    }
}
