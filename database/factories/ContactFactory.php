<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\ContactSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'contact_subject_id' => ContactSubject::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'content' => $this->faker->paragraph(),
            'reply_content' => null,
            'replied_at' => null,
            'replied_by' => null,
        ];
    }
}
