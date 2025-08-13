<?php

namespace App\Enums;

enum VoteType: string
{
    case UP_VOTE = 'upvote';
    case DOWN_VOTE = 'downvote';
}
