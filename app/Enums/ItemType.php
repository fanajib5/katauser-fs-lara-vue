<?php

namespace App\Enums;

enum ItemType: string
{
    case PLAN = 'plan';
    case CREDIT = 'credit';
    case CUSTOMPACKAGE = 'custom_package';
}
