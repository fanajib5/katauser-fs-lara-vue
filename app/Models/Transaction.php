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
        'type',
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

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'custom_package_details' => 'array',
            'metadata' => 'array',
            'total_amount' => 'decimal:2',
            'credit_amount' => 'decimal:2',
            'status' => TransactionStatus::class,
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\Transaction>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Plan,\App\Models\Transaction>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get all transaction items for this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\TransactionItem,\App\Models\Transaction>
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get all user credits generated from this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\UserCredit,\App\Models\Transaction>
     */
    public function userCredits(): HasMany
    {
        return $this->hasMany(UserCredit::class);
    }

    // ========== HAS ONE RELATIONS ==========

    /**
     * Get the subscription created from this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Subscription,\App\Models\Transaction>
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
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
        return $query->where('status', 'paid');
    }

    /**
     * Scope to filter pending transactions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query): Builder
    {
        return $query->where('status', 'pending');
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
            ->where('status', 'pending');
    }

    // ========== ACCESSORS ==========

    /**
     * Check if transaction is paid.
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if transaction is pending.
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at?->isPast() && $this->status === 'pending';
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format((float) $this->total_amount, 2) . ' ' . $this->currency;
    }
}
