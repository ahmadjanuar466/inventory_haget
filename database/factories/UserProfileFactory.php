<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'alamat' => fake()->streetAddress(),
            'no_telp' => fake()->numerify('08##########'),
            'avatars' => fake()->lexify('avatar_????.png'),
            'birth_date' => fake()->date('Y-m-d', '2010-12-31'),
            'users_id' => null,
        ];
    }
}
