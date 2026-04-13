<?php

namespace Database\Factories;

use App\Models\Lot;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lot>
 */
class LotFactory extends Factory
{
    protected $model = Lot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement([
            'colis',
            'palette_standard',
            'palette_non_standard',
            'caisse',
            'oeuvre_art',
            'hors_gabarit',
            'conteneur_groupage',
            'conteneur_complet',
            'vehicule'
        ]);

        $length = $this->faker->numberBetween(50, 200);
        $width = $this->faker->numberBetween(50, 120);
        $height = $this->faker->numberBetween(50, 200);
        $volume = ($length * $width * $height) / 1000000;

        return [
            'shipment_id' => Shipment::factory(),
            'type' => $type,
            'quantity' => $this->faker->numberBetween(1, 10),
            'weight' => $this->faker->randomFloat(2, 5, 500),
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'palette_type' => $type === 'palette_standard' ? $this->faker->randomElement(['80_120', '100_120']) : null,
            'container_type' => $type === 'conteneur_complet' ? '20ft' : null,
            'brand' => $type === 'vehicule' ? 'Peugeot' : null,
            'model' => $type === 'vehicule' ? '308' : null,
            'vehicle_weight' => $type === 'vehicule' ? '1.5t' : null,
            'is_rolling' => $type === 'vehicule' ? true : false,
            'is_stackable' => $this->faker->boolean(),
            'volume' => $volume,
        ];
    }
}
