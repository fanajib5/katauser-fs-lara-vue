<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

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

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'user_id' => 'integer',
            'transaction_id' => 'integer',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
        // Kolom FK: user_id
    }

    /**
     * Get the transaction that created this subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
        // Kolom FK: transaction_id
    }

    // ========== HAS ONE THROUGH RELATIONS ==========

    /**
     * Get the plan through the transaction.
     * Subscription -> Transaction -> Plan
     *
     * @return Plan|null
     */
    public function plan(): ?Plan
    {
        return $this->transaction?->plan;
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get the user credits associated with this subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userCredits(): HasMany
    {
        return $this->hasMany(UserCredit::class, 'subscription_id');
        // Hanya tambahkan jika kolom subscription_id benar-benar ada di tabel user_credits
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
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter inactive subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query): Builder
    {
        return $query->where('is_active', false);
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
        // Perbaiki logika: Subscription aktif DAN (belum berakhir ATAU tidak ada tanggal akhir)
        return $query->where('is_active', true)
                        ->where(function ($q) {
                            $q->where('end_date', '>', now())
                            ->orWhereNull('end_date');
                        });
    }

    // ========== ACCESSORS ==========

    /**
     * Check if subscription is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        // Perbaiki: Subscription kadaluarsa jika end_date ada dan di masa lalu
        return $this->end_date !== null && $this->end_date->isPast();
    }

    /**
     * Get days remaining until expiration.
     *
     * @return int|null
     */
    public function daysRemaining(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        $days = now()->diffInDays($this->end_date, false); // false untuk mendapatkan hasil negatif jika sudah lewat
        return max(0, $days); // Jika sudah lewat, kembalikan 0
    }

    /**
     * Check if subscription is currently active (active and not expired).
     * (Accessor baru untuk logika yang lebih jelas)
     *
     * @return bool
     */
    public function isActive(): bool
    {
            return $this->is_active && ($this->end_date === null || $this->end_date->isFuture());
    }
}
