<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredit extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     * This table only has created_at.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'public_id',
        'user_id',
        'transaction_id',
        'change_amount',
        'balance_after',
        'description',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'user_id' => 'integer',
            'transaction_id' => 'integer',
            'change_amount' => 'integer',
            'balance_after' => 'integer',
            'description' => 'string',
            'created_at' => 'datetime',
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
    * Get the user that owns this credit record.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
        // Kolom FK: user_id
    }

    /**
    * Get the transaction associated with this credit change.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
        // Kolom FK: transaction_id
    }
}
