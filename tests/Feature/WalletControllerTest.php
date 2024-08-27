<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use App\Repositories\WalletRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $walletRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletRepo = $this->app->make(WalletRepository::class);
        $this->user = User::factory()->create();
        $this->wallet = Wallet::factory()->create(['user_id' => $this->user]);
    }

    /** @test */
    public function it_deposits_funds_successfully()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->user, 'api')->postJson('/api/wallet/deposit', [
            'amount' => 100,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Funds deposited successfully.']);
    }

    /** @test */
    public function it_fails_to_deposit_funds_with_invalid_amount()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->user, 'api')->postJson('/api/wallet/deposit', [
            'amount' => -100,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_fails_to_transfer_funds_with_invalid_details()
    {

        $response = $this->actingAs($this->user, 'api')->postJson('/api/wallet/transfer', [
            'recipient_id' => 9999, // Invalid recipient ID
            'amount' => 50,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_retrieves_wallet_balance_successfully()
    {

        $response = $this->actingAs($this->user, 'api')->getJson('/api/wallet/balance');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Wallet balance retrieved successfully.']);
    }

    /** @test */
    public function it_fails_to_retrieve_wallet_balance_when_not_authenticated()
    {
        $response = $this->getJson('/api/wallet/balance');

        $response->assertStatus(401);
    }
}
