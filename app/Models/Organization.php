<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use TracksChanges;

    protected $fillable = [
        'name',
        'custom_domain',
        'domain_verified_at',
        'urls',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'urls' => 'array',
    ];

    protected $dates = [
        'domain_verified_at',
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
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }
}
