<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Transaction model class.
 *
 * Represents a financial transaction between users, including sender, recipient, amount, and fee.
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['sender_id', 'recipient_id', 'amount', 'fee'];
}
