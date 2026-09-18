<?php

namespace Database\Factories;

use App\Models\Obat;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Obat>
 */
class ObatFactory extends Factory
{
    protected $model = Obat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 1;
        
        $hargaBeli = $this->faker->numberBetween(5000, 50000);
        $hargaJual = $hargaBeli * 1.3; // Margin 30%
        
        return [
            'kd_obat' => 'OBT-' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
            'nm_obat' => $this->faker->words(3, true) . ' ' . $this->faker->randomElement(['500mg', '250mg', '100mg', '50ml']),
            'jenis' => $this->faker->randomElement(['tablet', 'kapsul', 'sirup', 'salep', 'injeksi']),
            'satuan' => $this->faker->randomElement(['strip', 'botol', 'tube', 'ampul', 'box']),
            'harga_beli' => $hargaBeli,
            'harga_jual' => $hargaJual,
            'stok' => $this->faker->numberBetween(10, 100),
            'kd_supplier' => Supplier::factory(),
            'gambar' => null,
        ];
    }

    /**
     * Indicate that the obat has low stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stok' => $this->faker->numberBetween(0, 5),
        ]);
    }

    /**
     * Indicate that the obat is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stok' => 0,
        ]);
    }
}
