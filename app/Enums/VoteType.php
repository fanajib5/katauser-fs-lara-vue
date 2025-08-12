<?php

namespace App\Enums;

enum VoteType: string
{
    case UPVOTE = 'upvote';
    case DOWNVOTE = 'downvote';
}
