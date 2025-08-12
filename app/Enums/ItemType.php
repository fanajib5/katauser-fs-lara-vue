<?php

namespace App\Enums;

enum ItemType: string
{
    case Plan = 'plan';
    case Credit = 'credit';
    case CustomPackage = 'custom_package';
}
