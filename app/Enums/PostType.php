<?php

namespace App\Enums;

enum PostType: string
{
    case FEATURE = 'feature';
    case BUG = 'bug';
    case IMPROVEMENT = 'improvement';
    case QUESTION = 'question';
    case SUGGESTION = 'suggestion';
    case OTHER = 'other';
}
