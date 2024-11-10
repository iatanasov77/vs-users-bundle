<?php namespace Vankosoft\UsersBundle\Component;

class UserInfo
{
    const TITLE_MISTER  = 'Mr.';
    const TITLE_MISSIS  = 'Mrs.';
    const TITLE_MISS    = 'Miss';
    
    public static function choices()
    {
        return [
            self::TITLE_MISTER  => 'mr',
            self::TITLE_MISSIS  => 'mrs',
            self::TITLE_MISS    => 'miss',
        ];
    }
    
    /** Get Title By Database Value */
    public static function title( $title )
    {
        return array_search ( $title, self::choices() );
    }
}
