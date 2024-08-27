<?php

namespace App\Repositories;

use App\Models\Wallet;

/**
 * WalletRepository class.
 *
 * Handles operations related to user wallets, including balance management and transactions.
 */
class WalletRepository extends BaseRepository
{
    /**
     * The transaction repository instance.
     *
     * @var \App\Repositories\TransactionRepository
     */
    protected $transactionRepo;

    /**
     * Create class instance.
     *
     *
     * @return void
     */
    public function __construct(Wallet $model, TransactionRepository $transactionRepo)
    {
        parent::__construct($model);
        $this->transactionRepo = $transactionRepo;
    }

    /**
     * Get the wallet for a specific user.
     *
     * @param  int  $userId
     * @return \App\Models\Wallet
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUserWallet($userId)
    {
        return $this->model->where('user_id', $userId)->firstOrFail();
    }

    /**
     * Deposit funds into a user's wallet.
     *
     * @param  int  $userId
     * @param  float  $amount
     * @return \App\Models\Wallet
     */
    public function deposit($userId, $amount)
    {
        $wallet = $this->getUserWallet($userId);
        $wallet->balance += $amount;
        $wallet->save();

        return $wallet;
    }

    /**
     * Transfer funds between users.
     *
     * @param  int  $senderId
     * @param  int  $recipientId
     * @param  float  $amount
     * @return array
     *
     * @throws \Exception
     */
    public function transfer($senderId, $recipientId, $amount)
    {
        $senderWallet = $this->getUserWallet($senderId);
        $recipientWallet = $this->getUserWallet($recipientId);

        if ($senderWallet->balance < $amount) {
            throw new \Exception('Insufficient balance.');
        }

        $fee = $amount > 25 ? 2.5 + (0.1 * $amount) : 0;

        $senderWallet->balance -= ($amount + $fee);
        $recipientWallet->balance += $amount;

        $senderWallet->save();
        $recipientWallet->save();

        // Save transaction
        $this->transactionRepo->createTransaction([
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'amount' => $amount,
            'fee' => $fee,
        ]);

        return ['sender' => $senderWallet, 'recipient' => $recipientWallet];
    }
}
