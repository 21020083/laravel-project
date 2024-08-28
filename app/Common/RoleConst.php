<?php

namespace App\Common;

class RoleConst
{
    public const SUPER_ADMIN = 0;
    public const MANAGER = 1;
    public const ACCOUNTANT = 2;
    public const STAFF = 3;
    public const ROLES = [self::MANAGER, self::ACCOUNTANT, self::STAFF];
}
