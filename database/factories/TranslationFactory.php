<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        return [
            'translation_key_id' => TranslationKey::factory(),
            'language_id' => Language::factory(),
            'value' => $this->faker->sentence(),
        ];
    }
}
