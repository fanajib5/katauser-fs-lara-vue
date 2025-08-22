<?php

namespace App\Enums;

enum PostSource: string
{
    case EMBED = 'embed';
    case PUBLIC_PAGE = 'public_page';
}
