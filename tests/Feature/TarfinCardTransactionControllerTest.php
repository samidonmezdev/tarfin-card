<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CurrencyType;
use App\Models\TarfinCard;
use App\Models\TarfinCardTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Notification;
use Queue;
use Tests\TestCase;

class TarfinCardTransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Artisan::call('passport:install');
        $this->user = User::factory()->create();
        Passport::actingAs(
            $this->user,
        );
        $this->card = TarfinCard::factory([
            'user_id'=>$this->user
        ])->create();

    }

    /**
     * @test
     */
    public function a_customer_can_create_a_tarfin_card_transaction(): void
    {
        Queue::fake();

        $body = [
            'amount' => 5000,
            'currency_code'=>CurrencyType::TRY
        ];
        // 2. Act 🏋🏻‍
        $response = $this->json('POST', route('tarfin-cards.tarfin-card-transactions.store',['tarfin_card'=>$this->card ]), $body,);
        dd($response);
        // 3. Assert ✅
        $response->assertStatus(201)->assertJsonStructure(['data' => [
            'amount', 'currency_code'
        ]]);
    }

    /**
     * @test
     */
    public function a_customer_can_not_create_a_tarfin_card_transaction_for_a_tarfin_card_of_another_customer(): void
    {
        $card = TarfinCard::factory()->create();
        $body = [
            'amount' => 5000,
            'currency_code'=>CurrencyType::TRY
        ];
        // 2. Act 🏋🏻‍
        $response = $this->json('POST', route('tarfin-cards.tarfin-card-transactions.store',['tarfin_card'=>$card ]), $body,);
        // 3. Assert ✅
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function a_customer_can_see_a_tarfin_card_transaction(): void
    {
        $cardTransaction = TarfinCardTransaction::factory([
            'tarfin_card_id'=>$this->card
        ])->create();

        // 2. Act 🏋🏻‍
        $response = $this->json('GET', route('tarfin-card-transactions.show',['tarfin_card_transaction'=>$cardTransaction ]),);
        // 3. Assert ✅
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function a_customer_can_not_see_a_tarfin_card_transaction_for_a_tarfin_card_of_another_customer(): void
    {
        $card = TarfinCard::factory()->create();
        $cardTransaction = TarfinCardTransaction::factory([
            'tarfin_card_id'=>$card
        ])->create();

        // 2. Act 🏋🏻‍
        $response = $this->json('GET', route('tarfin-card-transactions.show',['tarfin_card_transaction'=>$cardTransaction ]),);
        // 3. Assert ✅
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function a_customer_can_list_tarfin_card_transactions(): void
    {
        // 2. Act 🏋🏻‍
        $response = $this->json('GET', route('tarfin-cards.tarfin-card-transactions.index',['tarfin_card'=>$this->card ]),);
        // 3. Assert ✅
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function a_customer_can_not_list_tarfin_card_transactions_for_a_tarfin_card_of_another_customer(): void
    {
        $card = TarfinCard::factory()->create();
        // 2. Act 🏋🏻‍
        $response = $this->json('GET', route('tarfin-cards.tarfin-card-transactions.index',['tarfin_card'=>$card ]),);
        // 3. Assert ✅
        $response->assertStatus(403);
    }

    // THE MORE TESTS THE MORE POINTS 🏆
}
