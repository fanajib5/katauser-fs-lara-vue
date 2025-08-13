<?php

namespace App\Enums;

enum FeedbackPostStatus: string
{
    case OPEN = 'open';
    case PLANNED = 'planned';
    case COMPLETED = 'completed';
    case IN_PROGRESS = 'in_progress';
    case ARCHIVED = 'archived';
    case CLOSED = 'closed';
}
