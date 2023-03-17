<?php

namespace Database\Factories;

use App\Enums\CurrencyType;
use App\Enums\PaymentStatus;
use App\Enums\PaymentTerms;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->numberBetween(0,100000000),
            'outstanding_amount' => $this->faker->numberBetween(0,100000000),
            'currency_code' => $this->faker->randomElement(CurrencyType::cases()),
            'status' => $this->faker->randomElement(PaymentStatus::cases()),
            'processed_at' => $this->faker->dateTime(),
            'terms' => $this->faker->randomElement(PaymentTerms::cases())
        ];
    }
}
