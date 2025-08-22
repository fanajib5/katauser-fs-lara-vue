<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /** @use HasRoles<\Spatie\Permission\Models\Role, \Spatie\Permission\Models\Permission> */
    use HasRoles;

    /** @use Notifiable<\Illuminate\Notifications\Notifiable> */
    use Notifiable;

    /** @use SoftDeletes<\Illuminate\Database\Eloquent\SoftDeletes> */
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'assigned_as_admin_at',
        'assigned_as_admin_by',
        'organization_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'assigned_as_admin_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * User belongs to an organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organization,\App\Models\User>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * User was assigned as admin by another user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\User>
     */
    public function assignedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_as_admin_by');
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * User can have many members (in different organizations)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Member,\App\Models\User>
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * User can assign admin role to many other users
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\User,\App\Models\User>
     */
    public function assignedAdmins(): HasMany
    {
        return $this->hasMany(User::class, 'assigned_as_admin_by');
    }

    /**
     * User can publish many changelogs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Changelog,\App\Models\User>
     */
    public function publishedChangelogs(): HasMany
    {
        return $this->hasMany(Changelog::class, 'published_by');
    }

    /**
     * User can have many transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Transaction,\App\Models\User>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * User can have many subscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Subscription,\App\Models\User>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * User can have many credit records
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\UserCredit,\App\Models\User>
     */
    public function userCredits(): HasMany
    {
        return $this->hasMany(UserCredit::class);
    }

    /**
     * Audit trails where this user performed actions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\AuditTrail,\App\Models\User>
     */
     public function auditTrails(): HasMany
     {
         return $this->hasMany(AuditTrail::class, 'user_id', 'id');
     }

    // ========== HAS ONE RELATIONS ==========

    /**
     * Get the member profile for this user in their organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Member,\App\Models\User>
     */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    /**
     * User has one active subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Subscription,\App\Models\User>
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('is_active', true);
    }

    // ========== SCOPE METHODS ==========

    /**
     * Scope query untuk admin users
     *
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\User> $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\User>
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->whereNotNull('assigned_as_admin_at');
    }

    /**
     * Scope query untuk user dengan organization tertentu
     *
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\User> $query
     * @param int $organizationId
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\User>
     */
    public function scopeInOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope query untuk user yang bukan admin
     *
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\User> $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\User>
     */
    public function scopeNonAdmins(Builder $query): Builder
    {
        return $query->whereNull('assigned_as_admin_at');
    }

    // ========== ACCESSOR & MUTATOR METHODS ==========

    /**
     * Get user's current credit balance
     */
    public function getCurrentCreditBalance(): int
    {
        return $this->userCredits()->latest()->first()?->balance_after ?? 0;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->assigned_as_admin_at != null;
    }

    /**
     * Check if user has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }
}
