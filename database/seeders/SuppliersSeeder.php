<?php

namespace Database\Seeders;

use App\Models\Suppliers;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $row = [
            'code' => 'SUP-001',
            'name' => 'PT angin ribut',
            'contact_person' => 'Ari',
            'phone' => '087821910608',
            'email' => 'abc@email.com',
            'is_active' => 1
        ];

        Suppliers::create($row);
    }
}
