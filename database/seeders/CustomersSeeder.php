<?php

namespace Database\Seeders;

use App\Models\Customers;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $row = [
            'code' => 'CUST-001',
            'name' => 'Ari',
            'phone' => '087821910608',
            'address' => 'Aster Village Blok E.33',
            'customer_type_id' => 1,
            'is_active' => 1
        ];
        Customers::create($row);
    }
}
