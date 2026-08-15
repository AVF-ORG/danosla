<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => ['en' => $this->faker->unique()->country()],
            'iso2' => strtoupper($this->faker->unique()->lexify('??')),
            'international_code' => '+' . $this->faker->numberBetween(1, 999),
            'svg' => null,
        ];
    }
}
