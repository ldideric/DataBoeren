<?php

namespace Database\Factories;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use App\Models\Coupon;
use App\Models\Extra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'code' => strtoupper(fake()->unique()->bothify('????##')),
            'scope' => CouponScope::Accommodation,
            'extra_id' => null,
            'discount_type' => fake()->randomElement(DiscountType::cases()),
            'discount_value' => fake()->randomFloat(2, 5, 30),
            'expires_at' => fake()->optional(0.6)->dateTimeBetween('+1 month', '+1 year'),
            'max_uses' => fake()->optional()->numberBetween(10, 200),
            'uses_count' => 0,
        ];
    }

    public function expired(): static
    {
        return $this->state([
            'expires_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }

    public function exhausted(): static
    {
        return $this->state(fn () => [
            'max_uses' => 10,
            'uses_count' => 10,
        ]);
    }

    public function percent(): static
    {
        return $this->state([
            'discount_type' => DiscountType::Percent,
            'discount_value' => fake()->randomFloat(2, 5, 50),
        ]);
    }

    public function flat(): static
    {
        return $this->state([
            'discount_type' => DiscountType::Flat,
            'discount_value' => fake()->numberBetween(5, 30) * 100,
        ]);
    }

    public function freeExtra(?Extra $extra = null): static
    {
        return $this->state([
            'scope' => CouponScope::Extra,
            'extra_id' => $extra?->id ?? Extra::factory(),
            'discount_type' => DiscountType::Percent,
            'discount_value' => 100,
        ]);
    }
}
