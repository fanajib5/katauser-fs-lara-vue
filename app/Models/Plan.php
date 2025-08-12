<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use TracksChanges, SoftDeletes;

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

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'features' => 'array',
        ];
    }

    protected function features(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    // public function subscriptions()
    // {
    //     return $this->hasMany(Subscription::class);
    // }

    // public function features()
    // {
    //     return $this->hasMany(Feature::class);
    // }
}
