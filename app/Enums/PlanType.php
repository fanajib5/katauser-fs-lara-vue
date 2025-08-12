<?php

namespace App\Enums;

enum PlanType: string
{
    case Subscription = 'subscription';
    case Payg = 'payg';
    case Custom = 'custom';
}
