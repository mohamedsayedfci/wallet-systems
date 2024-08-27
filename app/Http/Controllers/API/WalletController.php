<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\WalletResource;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\Auth;

/**
 * WalletController class.
 *
 * Manages API endpoints related to wallet operations, including deposits, transfers, and balance checks.
 */
class WalletController extends Controller
{
    /**
     * The wallet repository instance.
     *
     * @var \App\Repositories\WalletRepository
     */
    protected $walletRepo;

    /**
     * Create a new controller instance.
     *
     *
     * @return void
     */
    public function __construct(WalletRepository $walletRepo)
    {
        $this->walletRepo = $walletRepo;
    }

    /**
     * Deposit funds into the authenticated user's wallet.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(DepositRequest $request)
    {

        $userId = Auth::id();
        $amount = $request->input('amount');

        try {
            $wallet = $this->walletRepo->deposit($userId, $amount);

            return $this->sendResponse(new WalletResource($wallet), 'Funds deposited successfully.');
        } catch (\Exception $e) {

            return $this->sendError('Deposit failed. Please try again.', 500);
        }
    }

    /**
     * Transfer funds from the authenticated user to another user.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(TransferRequest $request)
    {

        $senderId = Auth::id();
        $recipientId = $request->input('recipient_id');
        $amount = $request->input('amount');

        $wallets = $this->walletRepo->transfer($senderId, $recipientId, $amount);
        if (! $wallets) {
            return $this->sendError('Transfer failed. Please check the details and try again.', 500);

        }

        return $this->sendResponse([
            'sender' => new WalletResource($wallets['sender']),
            'recipient' => new WalletResource($wallets['recipient']),
        ], 'Transfer successful.');

    }

    /**
     * Get the balance of the authenticated user's wallet.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function balance()
    {
        $userId = Auth::id();

        try {
            $wallet = $this->walletRepo->getUserWallet($userId);

            return $this->sendResponse(new WalletResource($wallet), 'Wallet balance retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve wallet balance. Please try again.', 500);
        }
    }
}
