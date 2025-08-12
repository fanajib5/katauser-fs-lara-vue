<?php

namespace App\Models;

use App\Enums\VoteType;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;

    protected $fillable = [
        'feedback_post_id',
        'member_id',
        'type',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => VoteType::class,
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the feedback post that owns this vote.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\FeedbackPost,\App\Models\Vote>
     */
    public function feedbackPost(): BelongsTo
    {
        return $this->belongsTo(FeedbackPost::class);
    }

    /**
     * Get the member who cast this vote.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Member,\App\Models\Vote>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // ========== SCOPES ==========

    /**
     * Scope to filter upvotes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpvotes($query): Builder
    {
        return $query->where('type', 'upvote');
    }

    /**
     * Scope to filter downvotes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDownvotes($query): Builder
    {
        return $query->where('type', 'downvote');
    }

    // ========== ACCESSORS ==========

    /**
     * Check if this is an upvote.
     *
     * @return bool
     */
    public function getIsUpvoteAttribute(): bool
    {
        return $this->type === VoteType::UPVOTE->value;
    }

    /**
     * Check if this is a downvote.
     *
     * @return bool
     */
    public function getIsDownvoteAttribute(): bool
    {
        return $this->type === VoteType::DOWNVOTE->value;
    }
}
