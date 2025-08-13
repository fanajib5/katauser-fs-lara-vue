<?php

namespace App\Enums;

enum UserRole: string
{
    case DEVELOPER = 'developer'; // role that has full access rights on the platform
    case ADMIN = 'admin'; // role that has full access rights on the organization
    case MEMBER = 'member'; // role that is registered within the organization
    case GUEST = 'guest'; // role that is not registered yet
    case USER = 'user'; // role that does not belong to any organization
}
