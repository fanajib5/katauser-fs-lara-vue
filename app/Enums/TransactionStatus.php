<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Subscription = 'subscription';
    case Topup = 'topup';
    case Custom = 'custom';
}
