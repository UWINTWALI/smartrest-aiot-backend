<?php

namespace Database\Factories;

use App\Models\SensorReading;
use App\Models\PatientProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SensorReading>
 */
class SensorReadingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SensorReading::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sensorType = fake()->randomElement([
            'humidity', 'body_movement', 'posture', 'vibration', 'sleep_apnea'
        ]);

        return [
            'patient_id' => PatientProfile::factory(),
            'bed_id' => 'BED-' . fake()->numerify('#####'),
            'sensor_type' => $sensorType,
            'sensor_value' => $this->generateSensorValue($sensorType),
            'sensor_unit' => $this->getSensorUnit($sensorType),
            'timestamp' => fake()->dateTimeBetween('-1 month', 'now'),
            'additional_metadata' => $this->generateMetadata($sensorType),
        ];
    }

    /**
     * Generate realistic sensor values based on sensor type.
     */
    private function generateSensorValue(string $sensorType): float
    {
        return match ($sensorType) {
            'humidity' => fake()->randomFloat(1, 30.0, 70.0), // 30-70%
            'body_movement' => fake()->randomFloat(2, 0.0, 10.0), // 0-10 movement units
            'posture' => fake()->randomFloat(1, -180.0, 180.0), // -180 to 180 degrees
            'vibration' => fake()->randomFloat(3, 0.0, 5.0), // 0-5 vibration intensity
            'sleep_apnea' => fake()->randomFloat(0, 0.0, 100.0), // 0-100 events per hour
            default => fake()->randomFloat(2, 0.0, 100.0),
        };
    }

    /**
     * Get appropriate unit for sensor type.
     */
    private function getSensorUnit(string $sensorType): string
    {
        return match ($sensorType) {
            'humidity' => '%',
            'body_movement' => 'units/min',
            'posture' => 'degrees',
            'vibration' => 'Hz',
            'sleep_apnea' => 'events/hour',
            default => 'units',
        };
    }

    /**
     * Generate metadata based on sensor type.
     */
    private function generateMetadata(string $sensorType): array
    {
        $baseMetadata = [
            'device_id' => 'SENSOR-' . fake()->numerify('####'),
            'firmware_version' => 'v' . fake()->randomDigit() . '.' . fake()->randomDigit(),
            'battery_level' => fake()->numberBetween(10, 100),
        ];

        return match ($sensorType) {
            'humidity' => array_merge($baseMetadata, [
                'temperature' => fake()->randomFloat(1, 18.0, 25.0),
                'calibration_date' => fake()->date(),
            ]),
            'body_movement' => array_merge($baseMetadata, [
                'sensitivity' => fake()->randomElement(['low', 'medium', 'high']),
                'detection_threshold' => fake()->randomFloat(2, 0.1, 2.0),
            ]),
            'posture' => array_merge($baseMetadata, [
                'axis' => fake()->randomElement(['x', 'y', 'z']),
                'calibration_offset' => fake()->randomFloat(2, -5.0, 5.0),
            ]),
            'vibration' => array_merge($baseMetadata, [
                'frequency_range' => fake()->randomElement(['0-50Hz', '0-100Hz', '0-200Hz']),
                'amplitude' => fake()->randomFloat(3, 0.001, 1.0),
            ]),
            'sleep_apnea' => array_merge($baseMetadata, [
                'monitoring_duration' => fake()->numberBetween(30, 480), // minutes
                'detection_algorithm' => fake()->randomElement(['v1.0', 'v2.0', 'v2.1']),
            ]),
            default => $baseMetadata,
        };
    }

    /**
     * Create readings for a specific patient.
     */
    public function forPatient(PatientProfile $patient): static
    {
        return $this->state(fn (array $attributes) => [
            'patient_id' => $patient->patient_id,
        ]);
    }

    /**
     * Create readings for a specific bed.
     */
    public function forBed(string $bedId): static
    {
        return $this->state(fn (array $attributes) => [
            'bed_id' => $bedId,
        ]);
    }

    /**
     * Create humidity sensor readings.
     */
    public function humidity(): static
    {
        return $this->state(fn (array $attributes) => [
            'sensor_type' => 'humidity',
            'sensor_value' => fake()->randomFloat(1, 30.0, 70.0),
            'sensor_unit' => '%',
            'additional_metadata' => $this->generateMetadata('humidity'),
        ]);
    }

    /**
     * Create movement sensor readings.
     */
    public function movement(): static
    {
        return $this->state(fn (array $attributes) => [
            'sensor_type' => 'body_movement',
            'sensor_value' => fake()->randomFloat(2, 0.0, 10.0),
            'sensor_unit' => 'units/min',
            'additional_metadata' => $this->generateMetadata('body_movement'),
        ]);
    }

    /**
     * Create sleep apnea readings.
     */
    public function sleepApnea(): static
    {
        return $this->state(fn (array $attributes) => [
            'sensor_type' => 'sleep_apnea',
            'sensor_value' => fake()->randomFloat(0, 0.0, 100.0),
            'sensor_unit' => 'events/hour',
            'additional_metadata' => $this->generateMetadata('sleep_apnea'),
        ]);
    }

    /**
     * Create recent readings (last 24 hours).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'timestamp' => fake()->dateTimeBetween('-24 hours', 'now'),
        ]);
    }

    /**
     * Create readings with abnormal values (for testing alerts).
     */
    public function abnormal(): static
    {
        return $this->state(function (array $attributes) {
            $sensorType = $attributes['sensor_type'] ?? fake()->randomElement([
                'humidity', 'body_movement', 'posture', 'vibration', 'sleep_apnea'
            ]);

            $abnormalValue = match ($sensorType) {
                'humidity' => fake()->randomElement([
                    fake()->randomFloat(1, 10.0, 25.0), // Too low
                    fake()->randomFloat(1, 80.0, 95.0), // Too high
                ]),
                'sleep_apnea' => fake()->randomFloat(0, 50.0, 150.0), // High apnea events
                'body_movement' => fake()->randomFloat(2, 15.0, 30.0), // Excessive movement
                default => fake()->randomFloat(2, 90.0, 100.0),
            };

            return [
                'sensor_type' => $sensorType,
                'sensor_value' => $abnormalValue,
                'sensor_unit' => $this->getSensorUnit($sensorType),
                'additional_metadata' => array_merge(
                    $this->generateMetadata($sensorType),
                    ['alert_triggered' => true, 'alert_level' => 'high']
                ),
            ];
        });
    }
}
