<?php

namespace App\Enums;

final class Role extends Enum
{
    // Role Constants
    public const ADMIN = 'admin';
    public const CLIENT = 'client';
    public const USER = 'user';

    public const MOTHER = 'mother';
    public const FATHER = 'father';

    // Role Display Names
    public const NAME = [
        self::ADMIN => 'Admin',
        self::CLIENT => 'Client',
        self::USER => 'User',
    ];

    public const ParentRoleMap = [
        self::MOTHER => 'Mother',
        self::FATHER => 'Father',
    ];
}
