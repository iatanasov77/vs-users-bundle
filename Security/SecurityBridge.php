<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

final class SecurityBridge
{
    private TokenStorageInterface $tokenStorage;
    
    public function __construct(
        TokenStorageInterface $tokenStorage
    ) {
        $this->tokenStorage = $tokenStorage;
    }
    
    public function getUser(): ?UserInterface
    {
        $token  = $this->tokenStorage->getToken();
        
        return $token ? $token->getUser() : null;
    }
}