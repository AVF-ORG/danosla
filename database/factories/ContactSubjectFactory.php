<?php

namespace Database\Factories;

use App\Models\ContactSubject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactSubject>
 */
class ContactSubjectFactory extends Factory
{
    protected $model = ContactSubject::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);

        return [
            'name' => ['en' => $name],
            'slug' => ['en' => Str::slug($name)],
            'is_active' => true,
        ];
    }
}
