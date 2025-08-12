<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
        'role',
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
            'role' => UserRole::class,
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
     * User can create many plans
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Plan,\App\Models\User>
     */
    public function createdPlans(): HasMany
    {
        return $this->hasMany(Plan::class, 'created_by');
    }

    /**
     * User can update many plans
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Plan,\App\Models\User>
     */
    public function updatedPlans(): HasMany
    {
        return $this->hasMany(Plan::class, 'updated_by');
    }

    /**
     * User can delete many plans
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Plan,\App\Models\User>
     */
    public function deletedPlans(): HasMany
    {
        return $this->hasMany(Plan::class, 'deleted_by');
    }

    /**
     * User can create many organizations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Organization,\App\Models\User>
     */
    public function createdOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'created_by');
    }

    /**
     * User can update many organizations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Organization,\App\Models\User>
     */
    public function updatedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'updated_by');
    }

    /**
     * User can delete many organizations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Organization,\App\Models\User>
     */
    public function deletedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'deleted_by');
    }

    /**
     * User can create many feedback boards
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\FeedbackBoard,\App\Models\User>
     */
    public function createdFeedbackBoards(): HasMany
    {
        return $this->hasMany(FeedbackBoard::class, 'created_by');
    }

    /**
     * User can create many feedback posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\FeedbackPost,\App\Models\User>
     */
    public function createdFeedbackPosts(): HasMany
    {
        return $this->hasMany(FeedbackPost::class, 'created_by');
    }

    /**
     * User can create many comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Comment,\App\Models\User>
     */
    public function createdComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'created_by');
    }

    /**
     * User can create many votes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Vote,\App\Models\User>
     */
    public function createdVotes(): HasMany
    {
        return $this->hasMany(Vote::class, 'created_by');
    }

    /**
     * User can create many roadmap items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RoadmapItem,\App\Models\User>
     */
    public function createdRoadmapItems(): HasMany
    {
        return $this->hasMany(RoadmapItem::class, 'created_by');
    }

    /**
     * User can create many changelogs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Changelog,\App\Models\User>
     */
    public function createdChangelogs(): HasMany
    {
        return $this->hasMany(Changelog::class, 'created_by');
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
     * User can create many transaction items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\TransactionItem,\App\Models\User>
     */
    public function createdTransactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'created_by');
    }

    /**
     * User can create many items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Item,\App\Models\User>
     */
    public function createdItems(): HasMany
    {
        return $this->hasMany(Item::class, 'created_by');
    }

    // ========== HAS ONE RELATIONS ==========

    /**
     * User has one active subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Subscription,\App\Models\User>
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('active_status', true);
    }

    // ========== SCOPE METHODS ==========

    /**
     * Scope query untuk admin users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', UserRole::Admin);
    }

    /**
     * Scope query untuk user dengan organization tertentu
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $organizationId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    // ========== ACCESSOR & MUTATOR METHODS ==========

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if user is developer
     */
    public function isDeveloper(): bool
    {
        return $this->role === UserRole::Developer;
    }

    /**
     * Get user's current credit balance
     */
    public function getCurrentCreditBalance(): int
    {
        return $this->userCredits()->latest()->first()?->balance_after ?? 0;
    }
}
