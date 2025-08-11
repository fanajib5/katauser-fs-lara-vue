<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bio',
        'avatar',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
        'disabled_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'disabled_at',
    ];
}
