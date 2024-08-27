<?php

namespace App\Repositories;

use App\Models\Transaction;

/**
 * TransactionRepository class.
 *
 * Handles operations related to financial transactions between users.
 */
class TransactionRepository extends BaseRepository
{
    /**
     * Create class instance.
     *
     *
     * @return void
     */
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    /**
     * Get transactions for a specific user.
     *
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserTransactions($userId)
    {
        return $this->model->where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->get();
    }

    /**
     * Create a new transaction record.
     */
    public function createTransaction(array $attributes)
    {
        return $this->create($attributes);
    }
}
