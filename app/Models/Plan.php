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
        'version',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
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
