<?php

namespace App\Enums;

enum VoteType: string
{
    case Upvote = 'upvote';
    case Downvote = 'downvote';
}
