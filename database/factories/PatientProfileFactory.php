<?php

namespace Database\Factories;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientProfile>
 */
class PatientProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PatientProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => User::factory()->patient(),
            'national_id' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}[A-Z]'),
            'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
            'sex' => fake()->randomElement(['M', 'F', 'O']),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'health_conditions' => fake()->optional(0.7)->sentence(),
            'medications' => fake()->optional(0.6)->sentence(),
        ];
    }

    /**
     * Create a profile for an existing patient user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'patient_id' => $user->user_id,
        ]);
    }

    /**
     * Create a male patient profile.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'sex' => 'M',
        ]);
    }

    /**
     * Create a female patient profile.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'sex' => 'F',
        ]);
    }

    /**
     * Create a patient with specific health conditions.
     */
    public function withHealthConditions(string $conditions): static
    {
        return $this->state(fn (array $attributes) => [
            'health_conditions' => $conditions,
        ]);
    }

    /**
     * Create a patient with medications.
     */
    public function withMedications(string $medications): static
    {
        return $this->state(fn (array $attributes) => [
            'medications' => $medications,
        ]);
    }
}
