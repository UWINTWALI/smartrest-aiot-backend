<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $messageTypes = ['info', 'alert', 'reminder', 'notification', 'emergency'];
        $type = fake()->randomElement($messageTypes);

        return [
            'sender_id' => User::factory(),
            'recipient_id' => User::factory(),
            'title' => $this->generateTitle($type),
            'body' => $this->generateBody($type),
            'type' => $type,
            'is_read' => fake()->boolean(30), // 30% chance of being read
            'sent_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Generate appropriate title based on message type.
     */
    private function generateTitle(string $type): string
    {
        return match ($type) {
            'alert' => fake()->randomElement([
                'Sleep Quality Alert',
                'Abnormal Sensor Reading',
                'Device Malfunction Detected',
                'Patient Movement Alert',
                'Sleep Apnea Event Detected',
            ]),
            'reminder' => fake()->randomElement([
                'Medication Reminder',
                'Appointment Reminder',
                'Device Maintenance Due',
                'Health Check Reminder',
                'Sleep Schedule Reminder',
            ]),
            'notification' => fake()->randomElement([
                'Sleep Report Available',
                'New Doctor Assignment',
                'Profile Updated',
                'System Update Complete',
                'Data Sync Complete',
            ]),
            'emergency' => fake()->randomElement([
                'URGENT: Emergency Alert',
                'CRITICAL: Immediate Attention Required',
                'EMERGENCY: Patient Status Critical',
                'URGENT: Device Failure',
                'CRITICAL: Abnormal Readings',
            ]),
            'info' => fake()->randomElement([
                'Weekly Sleep Summary',
                'Device Status Update',
                'New Feature Available',
                'General Information',
                'Sleep Tips and Advice',
            ]),
            default => fake()->sentence(4),
        };
    }

    /**
     * Generate appropriate body based on message type.
     */
    private function generateBody(string $type): string
    {
        return match ($type) {
            'alert' => fake()->randomElement([
                'Your sleep sensor has detected unusual readings. Please check your device and contact your healthcare provider if you have concerns.',
                'Sleep apnea events have increased significantly over the past 24 hours. Consider consulting with your doctor.',
                'Your bed sensor is showing connectivity issues. Please ensure the device is properly connected.',
                'Abnormal movement patterns detected during sleep. This may indicate sleep disturbances.',
            ]),
            'reminder' => fake()->randomElement([
                'This is a friendly reminder to take your prescribed medication.',
                'You have an upcoming appointment with Dr. ' . fake()->lastName() . ' tomorrow at ' . fake()->time('H:i'),
                'Your sleep monitoring device requires routine maintenance. Please schedule a service appointment.',
                'Time for your weekly health check. Please review your sleep data and log any concerns.',
            ]),
            'notification' => fake()->randomElement([
                'Your weekly sleep analysis is now available in your dashboard. Review your progress and trends.',
                'You have been assigned a new healthcare provider. Welcome Dr. ' . fake()->lastName() . ' to your care team.',
                'Your profile information has been successfully updated with the latest changes.',
                'The system has been updated with new features to improve your sleep monitoring experience.',
            ]),
            'emergency' => fake()->randomElement([
                'URGENT: Critical readings detected from your sleep monitoring device. Please contact emergency services immediately.',
                'EMERGENCY: Your device has detected a potential medical emergency. Seek immediate medical attention.',
                'CRITICAL: Multiple sensor alerts triggered. Please check on the patient immediately.',
                'URGENT: Device malfunction during critical monitoring period. Manual intervention required.',
            ]),
            'info' => fake()->randomElement([
                'Here\'s your weekly sleep summary: Average sleep duration was ' . fake()->numberBetween(6, 9) . ' hours with ' . fake()->numberBetween(0, 5) . ' sleep disruptions.',
                'Your sleep monitoring device is functioning normally. Battery level: ' . fake()->numberBetween(60, 100) . '%',
                'New sleep analysis features are now available in your dashboard. Check them out to get better insights.',
                'Tips for better sleep: Maintain a consistent sleep schedule and create a comfortable sleep environment.',
            ]),
            default => fake()->paragraph(),
        };
    }

    /**
     * Create a message between specific users.
     */
    public function between(User $sender, User $recipient): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_id' => $sender->user_id,
            'recipient_id' => $recipient->user_id,
        ]);
    }

    /**
     * Create an unread message.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    /**
     * Create a read message.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Create an alert message.
     */
    public function alert(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'alert',
            'title' => $this->generateTitle('alert'),
            'body' => $this->generateBody('alert'),
        ]);
    }

    /**
     * Create an emergency message.
     */
    public function emergency(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'emergency',
            'title' => $this->generateTitle('emergency'),
            'body' => $this->generateBody('emergency'),
            'is_read' => false, // Emergency messages default to unread
        ]);
    }

    /**
     * Create a reminder message.
     */
    public function reminder(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'reminder',
            'title' => $this->generateTitle('reminder'),
            'body' => $this->generateBody('reminder'),
        ]);
    }

    /**
     * Create a notification message.
     */
    public function notification(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'notification',
            'title' => $this->generateTitle('notification'),
            'body' => $this->generateBody('notification'),
        ]);
    }

    /**
     * Create a recent message (last 24 hours).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'sent_at' => fake()->dateTimeBetween('-24 hours', 'now'),
        ]);
    }

    /**
     * Create messages from system (no sender).
     */
    public function fromSystem(): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_id' => null,
            'title' => 'System ' . fake()->randomElement(['Notification', 'Update', 'Alert']),
            'type' => 'notification',
        ]);
    }
}
