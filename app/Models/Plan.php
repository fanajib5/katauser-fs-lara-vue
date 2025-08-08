<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use TracksChanges;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }
}
