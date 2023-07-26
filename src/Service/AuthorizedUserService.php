<?php

namespace App\Service;

use App\Entity\Employee;
use App\Exception\AuthenticationException;

class AuthorizedUserService implements AuthorizedUserServiceInterface
{
    private static ?Employee $authorizedUser = null;

    public static function setAuthorizedUser(Employee $user): void
    {
        self::$authorizedUser = $user;
    }

    /**
     * @throws AuthenticationException
     */
    public static function getAuthorizedUser(): Employee
    {
        if (self::$authorizedUser == null) {
            throw new AuthenticationException();
        }

        return self::$authorizedUser;
    }
}