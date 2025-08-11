<?php

namespace App\Models;

use App\Enums\FeedbackPostSource;
use App\Enums\FeedbackPostStatus;
use App\Enums\FeedbackPostType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'public_id',
        'content',
        'feedback_board_id',
        'member_id',
        'source',
        'source_url',
        'type',
        'status',
        'metadata',
        'version',
        'set_to_public_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'set_to_public_at' => 'datetime',
            'source' => FeedbackPostSource::class,
            'status' => FeedbackPostStatus::class,
            'type' => FeedbackPostType::class,
        ];
    }
}
