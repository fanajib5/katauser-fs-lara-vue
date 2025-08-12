<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case SUBSCRIPTION = 'subscription';
    case TOPUP = 'topup';
    case CUSTOM = 'custom';
}
