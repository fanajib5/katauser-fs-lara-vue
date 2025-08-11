<?php

namespace App\Enums;

enum FeedbackPostType: string
{
    case Feature = 'feature';
    case Bug = 'bug';
    case Improvement = 'improvement';
    case Question = 'question';
    case Suggestion = 'suggestion';
    case Other = 'other';
}
