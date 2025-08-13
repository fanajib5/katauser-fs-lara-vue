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
        'item_id',
        'quantity',
        'price',
        'subtotal',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'transaction_id' => 'integer',
            'item_id' => 'integer',
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the transaction that owns this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
        // Kolom FK: transaction_id
    }

    /**
     * Get the item referenced by this transaction item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
        // Kolom FK: item_id
    }

    /**
     * Get the user who created this transaction item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo // Ganti nama method dari creator() ke createdBy() untuk konsistensi
    {
        return $this->belongsTo(User::class, 'created_by');
        // Kolom FK: created_by
    }

    /**
     * Get the user who last updated this transaction item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
        // Kolom FK: updated_by
    }

    /**
     * Get the user who deleted this transaction item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
        // Kolom FK: deleted_by (nullable)
    }

    // ========== SCOPES ==========

    /**
     * Scope to filter by transaction ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $transactionId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForTransaction($query, int $transactionId): Builder
    {
        return $query->where('transaction_id', $transactionId);
    }

    /**
     * Scope to filter by item ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $itemId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForItem($query, int $itemId): Builder
    {
        return $query->where('item_id', $itemId);
    }

    // ========== ACCESSORS ==========

    /**
     * Get formatted price with IDR currency.
     *
     * @return string
     */
    public function formattedPrice(): string
    {
        return 'Rp ' . number_format((float) $this->price, 2, ',', '.');
    }

    /**
     * Get formatted subtotal with IDR currency.
     *
     * @return string
     */
    public function formattedSubtotal(): string
    {
        return 'Rp ' . number_format((float) $this->subtotal, 2, ',', '.');
    }

    /**
     * Check if the quantity is greater than one.
     *
     * @return bool
     */
    public function isMultipleQuantity(): bool
    {
        return $this->quantity > 1;
    }
}
