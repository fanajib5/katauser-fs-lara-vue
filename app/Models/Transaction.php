<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use TracksChanges, SoftDeletes;

    protected $fillable = [
        'transaction_code',
        'user_id',
        'plan_id',
        'status',
        'payment_method',
        'total_amount',
        'currency',
        'custom_package_details',
        'credit_amount',
        'metadata',
        'paid_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'plan_id' => 'integer',
            'total_amount' => 'decimal:2',
            'credit_amount' => 'decimal:2',
            'status' => TransactionStatus::class,
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
            'currency' => 'string',
        ];
    }

    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    protected function custom_package_details(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the user that owns this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
        // Kolom FK: user_id
    }

    /**
     * Get the plan associated with this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
        // Kolom FK: plan_id (nullable)
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get all transaction items for this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
        // Kolom FK di TransactionItem: transaction_id
    }

    /**
     * Get all user credits generated from this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userCredits(): HasMany
    {
        return $this->hasMany(UserCredit::class);
        // Kolom FK di UserCredit: transaction_id
    }

    // ========== HAS ONE RELATIONS ==========

    /**
     * Get the subscription created from this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
        // Kolom FK di Subscription: transaction_id
    }

    // ========== SCOPES ==========

    /**
     * Scope to filter transactions by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param TransactionStatus $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, TransactionStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter paid transactions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid($query): Builder
    {
        return $query->where('status', TransactionStatus::PAID);
    }

    /**
     * Scope to filter pending transactions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query): Builder
    {
        return $query->where('status', TransactionStatus::PENDING);
    }

    /**
     * Scope to filter expired transactions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query): Builder
    {
        return $query->where('expires_at', '<', now())
            ->where('status', TransactionStatus::PENDING);
    }

    // ========== ACCESSORS ==========

    /**
     * Check if transaction is paid.
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->status === TransactionStatus::PAID;
    }

    /**
     * Check if transaction is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === TransactionStatus::PENDING;
    }

    /**
     * Check if transaction is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() && $this->status === TransactionStatus::PENDING;
    }

    /**
     * Get formatted total amount.
     *
     * @return string
     */
    public function formattedTotal(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 2, ',', '.'); // Format IDR
    }

    /**
     * Get the type of transaction based on associated data.
     * (Accessor tambahan untuk menentukan jenis transaksi)
     *
     * @return string
     */
    public function type(): string
    {
        // Logika sederhana berdasarkan skema:
        // - Jika plan_id tidak null, kemungkinan besar subscription
        // - Jika credit_amount > 0, kemungkinan besar top-up
        // - Jika keduanya ada, bisa jadi custom package
        // - Jika transaction_items ada, bisa lihat item_type-nya
        if ($this->plan_id) {
            return 'subscription';
        } elseif ($this->credit_amount > 0) {
            return 'topup';
        } elseif ($this->transactionItems->isNotEmpty()) {
            // Cek item pertama sebagai indikator kasar
            $firstItem = $this->transactionItems->item()->first();
            // Asumsikan TransactionItem memiliki relasi ke Item atau kolom type
            return $firstItem->type ?? 'unknown';
            // return 'item_purchase'; // Placeholder
        }
        return 'unknown';
    }
}
