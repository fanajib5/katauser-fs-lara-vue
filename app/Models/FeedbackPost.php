<?php

namespace App\Models;

use App\Enums\{FeedbackPostSource, FeedbackPostStatus, FeedbackPostType, VoteType};
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackPost extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;

    protected $fillable = [
        'public_id',
        'feedback_board_id',
        'member_id',
        'title',
        'content',
        'source',
        'source_url',
        'status',
        'type',
        'metadata',
        'set_to_public_at',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'set_to_public_at' => 'datetime',
            'source' => FeedbackPostSource::class,
            'status' => FeedbackPostStatus::class,
            'type' => FeedbackPostType::class,
            'metadata' => 'array',
            'public_id' => 'string',
            'feedback_board_id' => 'integer',
            'member_id' => 'integer',
            'version' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the feedback board that owns this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\FeedbackBoard,\App\Models\FeedbackPost>
     */
    public function feedbackBoard(): BelongsTo
    {
        return $this->belongsTo(FeedbackBoard::class);
    }

    /**
     * Get the member who created this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Member,\App\Models\FeedbackPost>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who created the feedback board.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\FeedbackPost>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the feedback board.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\FeedbackPost>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the feedback board.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\FeedbackPost>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get all votes for this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Vote,\App\Models\FeedbackPost>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get all upvotes for this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Vote,\App\Models\FeedbackPost>
     */
    public function upvotes(): HasMany
    {
        return $this->hasMany(Vote::class)->where('type', VoteType::UPVOTE);
    }

    /**
     * Get all downvotes for this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Vote,\App\Models\FeedbackPost>
     */
    public function downvotes(): HasMany
    {
        return $this->hasMany(Vote::class)->where('type', VoteType::DOWNVOTE);
    }

    /**
     * Get all comments for this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Comment,\App\Models\FeedbackPost>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all top-level comments (no parent).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Comment,\App\Models\FeedbackPost>
     */
    public function topLevelComments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    // ========== HAS ONE RELATIONS ==========

    /**
     * Get the top-level comment for this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Comment,\App\Models\FeedbackPost>
     */
    public function topComment(): HasOne
    {
        return $this->hasOne(Comment::class)->whereNull('parent_id');
    }

    /**
     * Get the roadmap item associated with this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\RoadmapItem,\App\Models\FeedbackPost>
     */
    public function roadmapItem(): HasOne
    {
        return $this->hasOne(RoadmapItem::class);
    }

    // ========== SCOPES ==========

    /**
     * Scope to filter posts by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param FeedbackPostStatus $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, FeedbackPostStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter posts by type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param FeedbackPostType $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithType($query, FeedbackPostType $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter public posts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query): Builder
    {
        return $query->whereNotNull('set_to_public_at');
    }

    /**
     * Scope to order by vote count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByVotes($query, string $direction = 'desc'): Builder
    {
        return $query->withCount(VoteType::cases())
            ->orderBy('upvotes_count', $direction);
    }

    // ========== ACCESSORS ==========

    /**
     * Get total vote count.
     */
    public function totalVotes(): int
    {
        return $this->upvotes()->count() - $this->downvotes()->count();
    }

    /**
     * Check if post is public.
     */
    public function isPublic(): bool
    {
        return $this->set_to_public_at !== null;
    }
}
