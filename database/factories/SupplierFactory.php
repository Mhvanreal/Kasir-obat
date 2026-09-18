<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 1;
        
        return [
            'kd_supplier' => 'SUP-' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
            'nm_supplier' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'kota' => $this->faker->city(),
            'telpon' => $this->faker->phoneNumber(),
        ];
    }
}
