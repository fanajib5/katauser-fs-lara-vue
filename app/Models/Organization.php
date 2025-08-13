<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough};
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;

    protected $fillable = [
        'public_id',
        'plan_id',
        'name',
        'slug',
        'subdomain',
        'custom_domain',
        'domain_verified_at',
        'urls',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'plan_id' => 'integer',
            'domain_verified_at' => 'datetime',
            'version' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    protected function urls(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($value) ? json_decode($value, true) : $value,
            set: fn ($value) => is_array($value) ? json_encode($value) : $value,
        );
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the plan that owns the organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
        // Kolom FK: plan_id
    }

    /**
     * Get the user who created the organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
        // Kolom FK: created_by
    }

    /**
     * Get the user who last updated the organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
        // Kolom FK: updated_by
    }

    /**
     * Get the user who deleted the organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
        // Kolom FK: deleted_by (nullable)
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get all users associated with this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
        // Kolom FK di User: organization_id
    }

    /**
     * Get all members of this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
        // Kolom FK di Member: organization_id
    }

    /**
     * Get all feedback boards for this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedbackBoards(): HasMany
    {
        return $this->hasMany(FeedbackBoard::class);
        // Kolom FK di FeedbackBoard: organization_id
    }

    /**
     * Get all roadmap items for this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roadmapItems(): HasMany
    {
        return $this->hasMany(RoadmapItem::class);
        // Kolom FK di RoadmapItem: organization_id
    }

    /**
     * Get all changelogs for this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changelogs(): HasMany
    {
        return $this->hasMany(Changelog::class);
        // Kolom FK di Changelog: organization_id
    }

    /**
     * Get all feedback posts for this organization.
     *
     * @return HasManyThrough
     */
    public function feedbackPosts(): HasManyThrough
    {
        return $this->hasManyThrough(FeedbackPost::class, FeedbackBoard::class);
    }


    // ========== SCOPES ==========

    /**
     * Scope to filter organizations with verified domains.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithVerifiedDomain($query): Builder
    {
        return $query->whereNotNull('domain_verified_at');
    }

    /**
     * Scope to filter organizations by plan type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $planId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithPlan($query, int $planId): Builder
    {
        return $query->where('plan_id', $planId);
    }

    // ========== ACCESSORS & MUTATORS ==========

    /**
     * Get the organization's full URL.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fullUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->custom_domain && $this->domain_verified_at) {
                    return "https://{$this->custom_domain}";
                }

                return "https://{$this->subdomain}.katauser.com";
            }
        );
    }

    /**
     * Check if organization has verified custom domain.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function hasVerifiedDomain(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->domain_verified_at !== null
        );
    }
}
