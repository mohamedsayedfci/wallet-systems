<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * AuthController class.
 *
 * Manages API endpoints related to user authentication, registration, and profile management.
 */
class AuthController extends Controller
{
    /**
     * The user repository instance.
     *
     * @var \App\Repositories\UserRepository
     */
    protected $userRepo;

    /**
     * Create a new controller instance.
     *
     *
     * @return void
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Register a new user.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->userRepo->createUser($request->validated());

            return $this->sendResponse(new UserResource($user), 'User registered successfully.', 201);
        } catch (\Exception $e) {
            return $this->sendError('Registration failed. Please try again.', 500);
        }
    }

    /**
     * Log in a user and return an access token.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return $this->sendError('Unauthorized', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->accessToken;

        return $this->sendResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful.');
    }

    /**
     * Log out the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $user = Auth::user();
            $user->tokens()->delete();

            return $this->sendResponse([], 'Successfully logged out.');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed. Please try again.', 500);
        }
    }

    /**
     * Update the authenticated user's profile.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $this->userRepo->updateProfile($user->id, $request->validated());

            return $this->sendResponse(new UserResource($user), 'Profile updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Profile update failed. Please try again.', 500);
        }
    }

    /**
     * Change the authenticated user's password.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return $this->sendError('Current password is incorrect', 403);
        }

        try {
            $this->userRepo->updateProfile($user->id, [
                'password' => Hash::make($request->new_password),
            ]);

            return $this->sendResponse([], 'Password updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Password change failed. Please try again.', 500);
        }
    }
}
