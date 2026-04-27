<?php

namespace Database\Factories;

use App\Models\ShipmentBid;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentBidFactory extends Factory
{
    protected $model = ShipmentBid::class;

    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'user_id' => User::factory(),
            'price' => $this->faker->randomFloat(2, 50, 5000),
            'latest_pickup_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'latest_pickup_time' => $this->faker->time('H:i'),
            'latest_delivery_date' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'latest_delivery_time' => $this->faker->time('H:i'),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected', 'cancelled', 'countered']),
        ];
    }
}
