<?php namespace VS\UsersBundle\Component;

class UserRole {
    const ROLE_SUPER_ADMIN  = 'ROLE_SUPER_ADMIN';
    const ROLE_ADMIN        = 'ROLE_ADMIN';
    const ROLE_USER         = 'ROLE_USER';
    
    public static function choices()
    {
        return [
            self::ROLE_SUPER_ADMIN  => self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN        => self::ROLE_ADMIN,
            self::ROLE_USER         => self::ROLE_USER,
        ];
    }
}
