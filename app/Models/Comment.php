<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /** @use TracksChanges<\App\Traits\TracksChanges> */
    use TracksChanges;

    /** @use SoftDeletes<\Illuminate\Database\Eloquent\SoftDeletes> */
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'feedback_post_id',
        'member_id',
        'content',
        'parent_id',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ========== RELATIONS ==========

    /**
     * Comment belongs to a feedback post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\FeedbackPost, \App\Models\Comment>
     */
    public function feedbackPost(): BelongsTo
    {
        return $this->belongsTo(FeedbackPost::class, 'feedback_post_id');
    }

    /**
     * Comment belongs to a member (the author).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Member, \App\Models\Comment>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * Comment was created by a user (admin or team member).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Comment>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Comment was updated by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Comment>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Comment was deleted by a user (nullable).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Comment>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Comment may have a parent comment (for nested/reply comments).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Comment, \App\Models\Comment>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Comment may have multiple replies (children comments).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Comment, \App\Models\Comment>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope to get only root comments (not replies).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Comment>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Comment>
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get only replies (not top-level comments).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Comment>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Comment>
     */
    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope to get comments for a specific feedback post.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Comment>  $query
     * @param  int  $feedbackPostId
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Comment>
     */
    public function scopeForFeedbackPost($query, int $feedbackPostId)
    {
        return $query->where('feedback_post_id', $feedbackPostId);
    }
}
