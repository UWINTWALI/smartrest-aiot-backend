<?php

namespace Database\Factories;

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DoctorProfile>
 */
class DoctorProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DoctorProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specialties = [
            'Sleep Medicine',
            'Cardiology',
            'Pulmonology',
            'Neurology',
            'Internal Medicine',
            'Emergency Medicine',
            'Family Medicine',
            'Psychiatry',
            'Geriatrics',
            'Critical Care Medicine'
        ];

        $institutions = [
            'City Medical Center',
            'General Hospital',
            'University Medical Center',
            'Regional Health System',
            'Community Hospital',
            'Metropolitan Medical Center',
            'St. Mary\'s Hospital',
            'Central Health Clinic'
        ];

        return [
            'doctor_id' => User::factory()->doctor(),
            'license_no' => 'MED' . fake()->unique()->numerify('######'),
            'specialty' => fake()->randomElement($specialties),
            'institution' => fake()->randomElement($institutions),
            'years_experience' => fake()->numberBetween(1, 40),
        ];
    }

    /**
     * Create a profile for an existing doctor user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'doctor_id' => $user->user_id,
        ]);
    }

    /**
     * Create a doctor with a specific specialty.
     */
    public function specialty(string $specialty): static
    {
        return $this->state(fn (array $attributes) => [
            'specialty' => $specialty,
        ]);
    }

    /**
     * Create a sleep medicine specialist.
     */
    public function sleepSpecialist(): static
    {
        return $this->specialty('Sleep Medicine');
    }

    /**
     * Create an experienced doctor (15+ years).
     */
    public function experienced(): static
    {
        return $this->state(fn (array $attributes) => [
            'years_experience' => fake()->numberBetween(15, 40),
        ]);
    }

    /**
     * Create a junior doctor (1-5 years).
     */
    public function junior(): static
    {
        return $this->state(fn (array $attributes) => [
            'years_experience' => fake()->numberBetween(1, 5),
        ]);
    }
}
