<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionItem extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;

    protected $fillable = [
        'public_id',
        'transaction_id',
        'type',
        'item_id',
        'quantity',
        'price',
        'subtotal',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the transaction that owns this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Transaction,\App\Models\TransactionItem>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the item referenced by this transaction item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Item,\App\Models\TransactionItem>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the user who created this transaction item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\TransactionItem>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ========== SCOPES ==========

    /**
     * Scope to filter by item type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithType($query, $type): Builder
    {
        return $query->where('type', $type);
    }

    // ========== ACCESSORS ==========

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return number_format((float) $this->subtotal, 2);
    }
}
