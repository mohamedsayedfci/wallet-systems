<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * TransactionResource class.
 *
 * Transforms the transaction model into an array for API responses.
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'recipient_id' => $this->recipient_id,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
