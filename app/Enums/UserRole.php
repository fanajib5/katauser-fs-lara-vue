<?php

namespace App\Enums;

enum UserRole: string
{
    case Developer = 'developer';
    case Admin = 'admin';
    case Member = 'member';
    case Guest = 'guest';
    case User = 'user';
}
