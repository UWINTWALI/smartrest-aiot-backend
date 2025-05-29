<?php

namespace Database\Factories;

use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SystemLog>
 */
class SystemLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SystemLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $severity = fake()->randomElement(['info', 'warning', 'error', 'critical']);
        
        return [
            'bed_id' => 'BED-' . fake()->numerify('#####'),
            'severity' => $severity,
            'message' => $this->generateMessage($severity),
            'logged_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Generate appropriate log message based on severity.
     */
    private function generateMessage(string $severity): string
    {
        return match ($severity) {
            'info' => fake()->randomElement([
                'Sensor reading recorded successfully',
                'Device status check completed',
                'Patient data synchronized',
                'Routine maintenance check passed',
                'System backup completed',
                'Device connectivity restored',
                'Sleep monitoring session started',
                'Sleep monitoring session ended',
                'Sensor calibration completed',
                'Data transmission successful',
            ]),
            'warning' => fake()->randomElement([
                'Sensor reading outside normal range',
                'Device battery level low (below 20%)',
                'Intermittent connectivity issues detected',
                'Sensor calibration may be required',
                'High humidity levels detected',
                'Unusual movement patterns observed',
                'Device response time slower than expected',
                'Memory usage approaching limit',
                'Network latency higher than normal',
                'Backup operation took longer than expected',
            ]),
            'error' => fake()->randomElement([
                'Failed to record sensor reading',
                'Device communication timeout',
                'Sensor malfunction detected',
                'Data synchronization failed',
                'Database connection error',
                'Authentication failure for device',
                'Invalid sensor data received',
                'File system error occurred',
                'Network connection lost',
                'Service restart required',
            ]),
            'critical' => fake()->randomElement([
                'CRITICAL: Multiple sensor failures detected',
                'CRITICAL: Device completely unresponsive',
                'CRITICAL: Patient monitoring interrupted',
                'CRITICAL: System-wide failure detected',
                'CRITICAL: Emergency alert system failure',
                'CRITICAL: Database corruption detected',
                'CRITICAL: Security breach attempt',
                'CRITICAL: Power supply failure',
                'CRITICAL: Main server down',
                'CRITICAL: Patient safety system offline',
            ]),
            default => fake()->sentence(),
        };
    }

    /**
     * Create logs for a specific bed.
     */
    public function forBed(string $bedId): static
    {
        return $this->state(fn (array $attributes) => [
            'bed_id' => $bedId,
        ]);
    }

    /**
     * Create info level logs.
     */
    public function info(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'info',
            'message' => $this->generateMessage('info'),
        ]);
    }

    /**
     * Create warning level logs.
     */
    public function warning(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'warning',
            'message' => $this->generateMessage('warning'),
        ]);
    }

    /**
     * Create error level logs.
     */
    public function error(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'error',
            'message' => $this->generateMessage('error'),
        ]);
    }

    /**
     * Create critical level logs.
     */
    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'critical',
            'message' => $this->generateMessage('critical'),
        ]);
    }

    /**
     * Create recent logs (last 24 hours).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'logged_at' => fake()->dateTimeBetween('-24 hours', 'now'),
        ]);
    }

    /**
     * Create logs from the last hour.
     */
    public function lastHour(): static
    {
        return $this->state(fn (array $attributes) => [
            'logged_at' => fake()->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    /**
     * Create sensor-related logs.
     */
    public function sensorRelated(): static
    {
        $messages = [
            'Humidity sensor reading: ' . fake()->randomFloat(1, 30, 70) . '%',
            'Movement sensor triggered: ' . fake()->randomFloat(2, 0, 10) . ' units',
            'Posture sensor reading: ' . fake()->randomFloat(1, -180, 180) . ' degrees',
            'Vibration sensor reading: ' . fake()->randomFloat(3, 0, 5) . ' Hz',
            'Sleep apnea detection: ' . fake()->randomFloat(0, 0, 50) . ' events/hour',
        ];

        return $this->state(fn (array $attributes) => [
            'message' => fake()->randomElement($messages),
            'severity' => fake()->randomElement(['info', 'warning']),
        ]);
    }

    /**
     * Create device maintenance logs.
     */
    public function maintenance(): static
    {
        $messages = [
            'Scheduled maintenance completed successfully',
            'Device firmware updated to latest version',
            'Sensor calibration performed',
            'Battery replacement completed',
            'Device cleaning and inspection completed',
            'Network configuration updated',
            'Security patches applied',
            'Performance optimization completed',
        ];

        return $this->state(fn (array $attributes) => [
            'message' => fake()->randomElement($messages),
            'severity' => 'info',
        ]);
    }

    /**
     * Create connectivity-related logs.
     */
    public function connectivity(): static
    {
        $messages = [
            'WiFi connection established',
            'Network connection lost',
            'Attempting to reconnect to server',
            'Connection restored after ' . fake()->numberBetween(1, 30) . ' seconds',
            'Weak signal strength detected',
            'Network configuration error',
            'DNS resolution failed',
            'Connection timeout occurred',
        ];

        return $this->state(fn (array $attributes) => [
            'message' => fake()->randomElement($messages),
            'severity' => fake()->randomElement(['info', 'warning', 'error']),
        ]);
    }
}
