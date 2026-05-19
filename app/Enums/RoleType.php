<?php

namespace App\Enums;

enum RoleType: string
{
    case SuperAdmin = 'super-admin';
    case Admin      = 'admin';
    case User       = 'user';
}
