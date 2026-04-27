<?php

namespace Database\Factories;

use App\Models\BidMessage;
use App\Models\ShipmentBid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BidMessageFactory extends Factory
{
    protected $model = BidMessage::class;

    public function definition(): array
    {
        return [
            'bid_id' => ShipmentBid::factory(),
            'user_id' => User::factory(),
            'message' => $this->faker->sentence(12),
            'is_read' => $this->faker->boolean(20),
        ];
    }
}
