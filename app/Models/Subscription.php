<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id',
        'user_id',
        'transaction_id',
        'is_active',
        'balance',
        'start_date',
        'end_date',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'balance' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the user that owns this subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\Subscription>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction that created this subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Transaction,\App\Models\Subscription>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the plan through transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<\App\Models\Plan,\App\Models\Transaction,\App\Models\Subscription>
     */
    public function plan(): HasOneThrough
    {
        return $this->hasOneThrough(Plan::class, Transaction::class, 'id', 'id', 'transaction_id', 'plan_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope to filter active subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query): Builder
    {
        return $query->where('active_status', true);
    }

    /**
     * Scope to filter inactive subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query): Builder
    {
        return $query->where('active_status', false);
    }

    /**
     * Scope to filter expired subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query): Builder
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Scope to filter current subscriptions (not expired).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrent($query): Builder
    {
        return $query->where('end_date', '>', now())
            ->orWhereNull('end_date');
    }

    // ========== ACCESSORS ==========

    /**
        * Check if subscription is active.
        */
    public function getIsActiveAttribute(): bool
    {
        return $this->active_status &&
                ($this->end_date === null || $this->end_date->isFuture());
    }

    /**
        * Check if subscription is expired.
        */
    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date?->isPast();
    }

    /**
        * Get days remaining.
        */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        return max(0, now()->diffInDays($this->end_date, false));
    }
}
