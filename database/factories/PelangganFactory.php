<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pelanggan>
 */
class PelangganFactory extends Factory
{
    protected $model = Pelanggan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 1;
        
        return [
            'kd_pelanggan' => 'PLG-' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
            'nm_pelanggan' => $this->faker->name(),
            'alamat' => $this->faker->address(),
            'kota' => $this->faker->city(),
            'telpon' => $this->faker->phoneNumber(),
        ];
    }
}
