<?php

namespace App\Enums;

enum RoadmapItemStatus: string
{
    case DRAFT = 'draft';
    case PLANNED = 'planned';
    case ARCHIVED = 'archived';
    case COMPLETED = 'completed';
    case IN_PROGRESS = 'in_progress';
    case CANCELLED = 'cancelled';
}
