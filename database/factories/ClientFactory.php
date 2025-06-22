<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'company' => $this->faker->optional(70)->company(), // 70% chance of having a company
            'type' => $this->faker->randomElement(['individual', 'business']),

            // Address
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->countryCode(),

            // Relationships
            'user_id' => User::inRandomOrder()->first()?->id, // Random existing user or null
            'supplier_id' => $this->faker->boolean(30)
                ? Supplier::inRandomOrder()->first()?->id
                : null, // 30% chance of being a supplier

            // Financial
            'credit_limit' => $this->faker->boolean(40)
                ? $this->faker->randomFloat(2, 1000, 50000)
                : null, // 40% chance of having credit
            'payment_terms' => $this->faker->randomElement(['net_15', 'net_30', 'cod']),

            // Metadata
            'notes' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(90), // 90% active
        ];
    }

    // States for specific scenarios
    public function configure()
    {
        return $this->afterMaking(function (Client $client) {
            // Ensure business clients have company names
            if ($client->type === 'business' && empty($client->company)) {
                $client->company = $this->faker->company();
            }
        });
    }

    // Factory States
    public function individual()
    {
        return $this->state(['type' => 'individual']);
    }

    public function business()
    {
        return $this->state([
            'type' => 'business',
            'company' => $this->faker->company(),
        ]);
    }

    public function withUser(?User $user = null)
    {
        return $this->state([
            'user_id' => $user ? $user->id : User::factory(),
        ]);
    }
}
