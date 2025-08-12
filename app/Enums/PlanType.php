<?php

namespace App\Enums;

enum PlanType: string
{
    case SUBSCRIPTION = 'subscription';
    case PAYG = 'payg';
    case CUSTOM = 'custom';
}
