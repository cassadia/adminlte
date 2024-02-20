<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class VehicleFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kd_motor' => fake()->name(),
            'nm_motor' => fake()->unique()->safeEmail(),
            'tahun' => now(),
            'no_seri_mesin' => fake()->unique()->no_seri_mesin(),
            'no_seri_angka' => fake()->unique()->no_seri_angka(),
            'status' => fake()->status()
        ];
    }
}
