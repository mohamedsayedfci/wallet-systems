<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepo;

    private const PASS = 'Test@2025';

    public function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
        $this->userRepo = $this->app->make(UserRepository::class);

        $this->user = User::factory()->create([
            'password' => Hash::make(self::PASS),
            'email_verified_at' => now(),
        ]);

    }

    /** @test */
    public function it_registers_a_user_successfully()
    {
        $user = User::factory()->make();
        $response = $this->postJson('/api/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully.']);
    }

    /** @test */
    public function it_fails_to_register_a_user_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
    }
}
