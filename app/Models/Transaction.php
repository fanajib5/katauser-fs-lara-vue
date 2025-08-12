<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use TracksChanges, SoftDeletes;

    protected $fillable = [
        'public_id',
        'user_id',
        'type',
        'plan_id',
        'status',
        'amount',
        'custom_package_details',
        'credit_amount',
        'payment_reference',
        'payment_details',
        'paid_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
        'type' => TransactionType::class,
        'payment_details' => 'array',
    ];

    protected function payment_details(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }
}
