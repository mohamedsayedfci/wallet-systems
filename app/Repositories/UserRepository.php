<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;

/**
 * UserRepository class.
 *
 * Handles operations related to user management, including authentication, registration, and profile updates.
 */
class UserRepository extends BaseRepository
{
    /**
     * Create class instance.
     *
     *
     * @return void
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new user and hash the password.
     */
    public function createUser(array $attributes): User
    {
        $attributes['password'] = Hash::make($attributes['password']);
        $user = $this->create($attributes);
        // Create wallet for the user
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        return $user;
    }

    /**
     * Update user's profile information.
     */
    public function updateProfile(int $id, array $attributes): void
    {
        $user = $this->findById($id);
        if (isset($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }
        $user->update($attributes);
    }

    /**
     * Verify user email.
     */
    public function verifyEmail(int $id): void
    {
        $user = $this->findById($id);
        $user->markEmailAsVerified();
    }
}
