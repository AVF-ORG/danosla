<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => $this->faker->sentence(10),
            'comment' => $this->faker->optional()->paragraph(),
            'total_value' => $this->faker->randomFloat(2, 100, 10000),
            'total_volume' => 0, // Calculated from lots
            'total_weight' => 0, // Calculated from lots
            'pickup_address' => $this->faker->address(),
            'pickup_options' => ['hayon', 'grue'],
            'delivery_address' => $this->faker->address(),
            'delivery_options' => ['hayon'],
            'latest_pickup_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'latest_pickup_time' => '09:00',
            'latest_delivery_date' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'latest_delivery_time' => '17:00',
            'validity_date' => $this->faker->dateTimeBetween('now', '+3 days'),
            'delivery_price' => $this->faker->randomFloat(2, 50, 2000),
            'requirements' => [],
            'status' => $this->faker->randomElement(['pending', 'active', 'completed', 'cancelled']),
        ];
    }
}
