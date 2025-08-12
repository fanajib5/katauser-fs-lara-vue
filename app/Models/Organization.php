<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    protected $appends = [
        'version',
    ];

    protected function casts(): array
    {
        return [
            'domain_verified_at' => 'datetime',
            'urls' => 'array',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organization,\App\Models\Plan>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get all users associated with this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\User>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all members of this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Member>
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get all feedback boards for this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\FeedbackBoard>
     */
    public function feedbackBoards(): HasMany
    {
        return $this->hasMany(FeedbackBoard::class);
    }

    /**
     * Get all roadmap items for this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RoadmapItem>
     */
    public function roadmapItems(): HasMany
    {
        return $this->hasMany(RoadmapItem::class);
    }

    /**
     * Get all changelogs for this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Changelog>
     */
    public function changelogs(): HasMany
    {
        return $this->hasMany(Changelog::class);
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
     */
    protected function hasVerifiedDomain(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->domain_verified_at !== null
        );
    }
}
