<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repositories\TransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $transactionRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionRepo = $this->app->make(TransactionRepository::class);
    }

    /** @test */
    public function it_retrieves_transaction_history_successfully()
    {
        $user = User::factory()->create();

        // Assuming you have some transactions in your repository
        $response = $this->actingAs($user, 'api')->getJson('/api/transactions');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction history retrieved successfully.']);
    }

    /** @test */
    public function it_fails_to_retrieve_transaction_history_when_not_authenticated()
    {
        $response = $this->getJson('/api/transactions');

        $response->assertStatus(401);
    }
}
