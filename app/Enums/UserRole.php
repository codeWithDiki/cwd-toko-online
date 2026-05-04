<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';

    case Partner = 'partner';

    case Customer = 'customer';
}