<?php

namespace App\Enums;

enum RoadmapItemStatus: string
{
    case Draft = 'draft';
    case Planned = 'planned';
    case Archived = 'archived';
    case Completed = 'completed';
    case InProgress = 'in_progress';
    case Cancelled = 'cancelled';
}
