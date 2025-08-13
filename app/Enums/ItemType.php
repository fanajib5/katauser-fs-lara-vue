<?php

namespace App\Enums;

enum ItemType: string
{
    case PLAN = 'plan';
    case CREDIT = 'credit';
    case CUSTOM_PACKAGE = 'custom_package';
}
