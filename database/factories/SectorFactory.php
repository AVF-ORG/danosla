<?php

namespace Database\Factories;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sector>
 */
class SectorFactory extends Factory
{
    protected $model = Sector::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->jobTitle();

        return [
            'name' => ['en' => $name],
            'slug' => ['en' => Str::slug($name)],
            'is_active' => true,
        ];
    }
}
