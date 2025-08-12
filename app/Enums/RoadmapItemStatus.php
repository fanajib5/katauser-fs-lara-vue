<?php

namespace App\Enums;

enum RoadmapItemStatus: string
{
    case DRAFT = 'draft';
    case PLANNED = 'planned';
    case ARCHIVED = 'archived';
    case COMPLETED = 'completed';
    case INPROGRESS = 'in_progress';
    case CANCELLED = 'cancelled';
}
