<?php

namespace App\Models;

use App\Enums\RoadmapItemStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoadmapItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'public_id',
        'organization_id',
        'feedback_post_id',
        'title',
        'content',
        'status',
        'version',
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
            'status' => RoadmapItemStatus::class,
        ];
    }
}
