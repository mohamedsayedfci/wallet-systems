<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Auth;

/**
 * TransactionController class.
 *
 * Manages API endpoints related to financial transactions, including viewing transaction history.
 */
class TransactionController extends Controller
{
    /**
     * The transaction repository instance.
     *
     * @var \App\Repositories\TransactionRepository
     */
    protected $transactionRepo;

    /**
     * Create a new controller instance.
     *
     *
     * @return void
     */
    public function __construct(TransactionRepository $transactionRepo)
    {
        $this->transactionRepo = $transactionRepo;
    }

    /**
     * Get the transaction history for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userId = Auth::id();

        try {
            $transactions = $this->transactionRepo->getUserTransactions($userId);

            return $this->sendResponse(
                TransactionResource::collection($transactions),
                'Transaction history retrieved successfully.'
            );
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve transaction history. Please try again.', 500);
        }
    }
}
