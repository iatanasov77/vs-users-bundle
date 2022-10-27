<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ApiManager
{
    /** @var TokenStorageInterface */
    private $tokenStorage;
    
    /** @var JWTTokenManagerInterface */
    private $jwtManager;
    
    public function __construct( TokenStorageInterface $tokenStorage, JWTTokenManagerInterface $jwtManager )
    {
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager   = $jwtManager;
        
    }
    
    public function getToken()
    {
        $decodedJwtToken = $this->jwtManager->decode( $this->tokenStorage->getToken() );
        
        return $decodedJwtToken;
    }
}
