<?php

namespace App\Enums;

enum PostStatus: string
{
    case OPEN = 'open';
    case PLANNED = 'planned';
    case COMPLETED = 'completed';
    case IN_PROGRESS = 'in_progress';
    case ARCHIVED = 'archived';
    case CANCELLED = 'cancelled';
    case CLOSED = 'closed';
}
