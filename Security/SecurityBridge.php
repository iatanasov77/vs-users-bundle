<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityBridge
{
    private TokenStorageInterface $tokenStorage;
    
    public function __construct(
        TokenStorageInterface $tokenStorage
    ) {
        $this->tokenStorage = $tokenStorage;
    }
    
    public function getUser()
    {
        $token  = $this->tokenStorage->getToken();
        
        return $token ? $token->getUser() : null;
    }
}