<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'name' => ['en' => $this->faker->unique()->city() . ' Region'],
            'code' => strtoupper($this->faker->unique()->lexify('REG-????')),
            'description' => ['en' => $this->faker->sentence()],
        ];
    }
}
