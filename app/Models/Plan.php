<?php

namespace App\Models;

use App\Enums\PlanType;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'price',
        'duration_days',
        'included_credits',
        'is_active',
        'features',
        'version',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => PlanType::class,
            'price' => 'decimal:2',
            'included_credits' => 'decimal:2',
            'is_active' => 'boolean',
            'features' => 'array',
            'version' => 'integer',
        ];
    }

    /**
     * Features attribute accessor and mutator
     */
    protected function features(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($value) ? json_decode($value, true) : $value,
            set: fn ($value) => is_array($value) ? json_encode($value) : $value,
        );
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get all organizations using this plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Organization,\App\Models\Plan>
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * Get all transactions for this plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Transaction,\App\Models\Plan>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all items associated with this plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Item,\App\Models\Plan>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    // ========== SCOPE METHODS ==========

    /**
     * Scope query to only include active plans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query to only include inactive plans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope query for specific plan type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Enums\PlanType $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, PlanType $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope query for subscription plans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubscription($query): Builder
    {
        return $query->where('type', PlanType::Subscription);
    }

    /**
     * Scope query for pay-as-you-go plans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePayg($query): Builder
    {
        return $query->where('type', PlanType::Payg);
    }

    /**
     * Scope query for custom plans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCustom($query): Builder
    {
        return $query->where('type', PlanType::Custom);
    }

    /**
     * Scope query for plans with price range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $minPrice
     * @param float $maxPrice
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePriceBetween($query, $minPrice, $maxPrice): Builder
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope query for plans with credits.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCredits($query): Builder
    {
        return $query->whereNotNull('included_credits')
                    ->where('included_credits', '>', 0);
    }

    // ========== ACCESSOR METHODS ==========

    /**
     * Get formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }

    /**
     * Get formatted included credits.
     */
    public function getFormattedCreditsAttribute(): string
    {
        if (!$this->included_credits) {
            return 'No credits included';
        }

        return number_format((float)$this->included_credits, 0, ',', '.') . ' credits';
    }

    /**
     * Get duration in human readable format.
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->duration_days) {
            return null;
        }

        $days = (int) $this->duration_days;

        if ($days >= 365) {
            $years = intval($days / 365);
            return $years . ' ' . ($years > 1 ? 'years' : 'year');
        }

        if ($days >= 30) {
            $months = intval($days / 30);
            return $months . ' ' . ($months > 1 ? 'months' : 'month');
        }

        return $days . ' ' . ($days > 1 ? 'days' : 'day');
    }

    /**
     * Check if plan is subscription type.
     */
    public function isSubscription(): bool
    {
        return $this->type === PlanType::Subscription;
    }

    /**
     * Check if plan is pay-as-you-go type.
     */
    public function isPayg(): bool
    {
        return $this->type === PlanType::Payg;
    }

    /**
     * Check if plan is custom type.
     */
    public function isCustom(): bool
    {
        return $this->type === PlanType::Custom;
    }

    /**
     * Check if plan has specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];

        if (is_array($features)) {
            return in_array($feature, $features) ||
                   (isset($features[$feature]) && $features[$feature] === true);
        }

        return false;
    }

    /**
     * Get feature value or default.
     *
     * @param string $feature
     * @param mixed $default
     * @return mixed
     */
    public function getFeatureValue(string $feature, $default = null): mixed
    {
        $features = $this->features ?? [];

        if (is_array($features)) {
            return $features[$feature] ?? $default;
        }

        return $default;
    }

    /**
     * Get all feature names.
     *
     * @return array<string>
     */
    public function getFeatureNames(): array
    {
        $features = $this->features ?? [];

        if (is_array($features)) {
            return array_keys($features);
        }

        return [];
    }

    // ========== BUSINESS LOGIC METHODS ==========

    /**
     * Calculate total organizations using this plan.
     */
    public function getTotalOrganizations(): int
    {
        return $this->organizations()->count();
    }

    /**
     * Calculate total revenue from this plan.
     */
    public function getTotalRevenue(): float
    {
        return $this->transactions()
                   ->where('status', 'paid')
                   ->sum('total_amount');
    }

    /**
     * Get active organizations count.
     */
    public function getActiveOrganizationsCount(): int
    {
        return $this->organizations()
                   ->whereNull('deleted_at')
                   ->count();
    }

    /**
     * Check if plan can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->getActiveOrganizationsCount() === 0;
    }

    /**
     * Activate the plan.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the plan.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Duplicate plan with new name.
     */
    public function duplicate(string $newName): self
    {
        $attributes = $this->toArray();
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at'], $attributes['deleted_at']);

        $attributes['name'] = $newName;
        $attributes['is_active'] = false; // New duplicated plan starts as inactive
        $attributes['version'] = 1; // Reset version
        $attributes['created_by'] = auth()?->id();
        $attributes['updated_by'] = auth()?->id();

        return self::create($attributes);
    }
}
