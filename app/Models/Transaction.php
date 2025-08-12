<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use TracksChanges, SoftDeletes;

    protected $fillable = [
        'transaction_code',
        'user_id',
        'type',
        'plan_id',
        'status',
        'payment_method',
        'total_amount',
        'currency',
        'custom_package_details',
        'credit_amount',
        'metadata',
        'paid_at',
        'expires_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TransactionStatus::class,
            'metadata' => 'array',
        ];
    }

    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }
}
