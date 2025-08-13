<?php

namespace App\Enums;

enum FeedbackPostType: string
{
    case FEATURE = 'feature';
    case BUG = 'bug';
    case IMPROVEMENT = 'improvement';
    case QUESTION = 'question';
    case SUGGESTION = 'suggestion';
    case OTHER = 'other';
}
