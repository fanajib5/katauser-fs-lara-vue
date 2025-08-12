<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id',
        'user_id',
        'transaction_id',
        'change_amount',
        'balance_after',
        'description',
    ];

    protected $hidden = [
        'created_at',
    ];
}
