<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\WalletTransactionType;

#[Fillable(['wallet_id', 'amount', 'type', 'balance_after', 'reference_type','reference_id','description'])]

class WalletTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\WalletTransactionFactory> */
    use HasFactory;

    protected $casts = [
        'type' => WalletTransactionType::class,
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
