<?php

namespace App\Enums;

enum FeedbackPostStatus: string
{
    case Open = 'open';
    case Planned = 'planned';
    case Completed = 'completed';
    case InProgress = 'in_progress';
    case Archived = 'archived';
    case Closed = 'closed';
}
