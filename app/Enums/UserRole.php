<?php

namespace App\Enums;

enum UserRole: string
{
    case DEVELOPER = 'developer'; // role yang memiliki hak akses penuh pada platform
    case ADMIN = 'admin'; // role yang memiliki hak akses penuh pada organisasi
    case MEMBER = 'member'; // role yang sudah terdaftar dalam organisasi
    case GUEST = 'guest'; // role yang belum daftar
    case USER = 'user'; // role yang tidak termasuk dalam organisasi manapun
}
