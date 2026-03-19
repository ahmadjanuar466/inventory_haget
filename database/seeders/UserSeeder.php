<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // User::factory()->count(1000)->create();
        $faker = fake();

        $user = User::firstOrCreate(
            ['email' => 'admin@nagabendu.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('N@gabendu666!'),
                'email_verified_at' => now(),
            ],
        );

        UserProfile::updateOrCreate(
            ['users_id' => $user->id],
            [
                'nama_lengkap' => 'Administrator',
                'email' => $user->email,
                'alamat' => null,
                'no_telp' => $faker->numerify('08##########'),
                'avatars' => $faker->lexify('avatar_????.png'),
                'birth_date' => null,
            ],
        );
    }
}
