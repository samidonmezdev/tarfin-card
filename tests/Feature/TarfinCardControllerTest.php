<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TarfinCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Notification;
use Tests\TestCase;

class TarfinCardControllerTest extends TestCase
{
    use RefreshDatabase;
    private $user;
    protected function setUp(): void
    {
        parent::setUp();
        \Artisan::call('passport:install');
        $this->user = User::factory()->create();
        Passport::actingAs(
            $this->user,
        );
    }

    /**
     * @test
     */
    public function a_customer_can_create_a_tarfin_card(): void
    {

        // 1. Arrange 🏗
        $body = [
            'type' => 'Visa'
        ];
        // 2. Act 🏋🏻‍
        $response = $this->json('POST', route('tarfin-cards.store'), $body,);
        // 3. Assert ✅
        $response->assertStatus(201)->assertJsonStructure(['data' => [
            'id', 'number', 'type', 'expiration_date', 'is_active'
        ]]);
    }

    /**
     * @test
     */
    public function a_customer_can_not_create_an_invalid_tarfin_card(): void
    {
        $body = [
            'type' => 'Test'
        ];
        // 2. Act 🏋🏻‍
        $response = $this->json('POST', route('tarfin-cards.store'), $body);
        // 3. Assert ✅
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function a_customer_can_see_a_tarfin_card(): void
    {
        $card = TarfinCard::factory()->create([
            'user_id'=>$this->user
        ]);
        // 2. Act 🏋🏻‍
        $response = $this->json('GET', route('tarfin-cards.show',['tarfin_card'=>$card]));
        // 3. Assert ✅
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function a_customer_can_not_see_a_tarfin_card_of_another_customer(): void
    {
        $card = TarfinCard::factory()->create();
        // 2. Act 🏋🏻‍
        $response = $this->json('GET', route('tarfin-cards.show',['tarfin_card'=>$card]));
        // 3. Assert ✅
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function a_customer_can_list_tarfin_cards(): void
    {

    }

    /**
     * @test
     */
    public function a_customer_can_activate_the_tarfin_card(): void
    {
         $card = TarfinCard::factory()->create([
            'user_id'=>$this->user
        ]);

        $body = [
            'is_active' => true
        ];
        // 2. Act 🏋🏻‍
        $response = $this->json('PUT', route('tarfin-cards.update',['tarfin_card'=>$card]),$body);
        // 3. Assert ✅
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function a_customer_can_deactivate_the_tarfin_card(): void
    {
         $card = TarfinCard::factory()->create([
            'user_id'=>$this->user
        ]);
        $body = [
            'is_active' => false
        ];
        // 2. Act 🏋🏻‍
        $response = $this->json('PUT', route('tarfin-cards.update',['tarfin_card'=>$card]),$body);
        // 3. Assert ✅
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function a_customer_can_delete_a_tarfin_card(): void
    {
        Notification::fake();
        $card = TarfinCard::factory()->create([
            'user_id'=>$this->user
        ]);

        // 2. Act 🏋🏻‍
        $response = $this->json('Delete', route('tarfin-cards.destroy',['tarfin_card'=>$card]));
        // 3. Assert ✅
        $response->assertStatus(200);
    }

    // THE MORE TESTS THE MORE POINTS 🏆
}
