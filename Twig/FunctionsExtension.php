<?php namespace Vankosoft\UsersBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

class FunctionsExtension extends AbstractExtension
{
    /** @var SecurityBridge */
    private $securityBridge;
    
    public function __construct( SecurityBridge $securityBridge )
    {
        $this->securityBridge   = $securityBridge;
    }
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction( 'vs_show_user', [$this, 'showUser'] ),
        ];
    }
    
    public function showUser( UserInterface $user ): bool
    {
        $currentUser    = $this->securityBridge->getUser();
        if ( ! $currentUser ) {
            return false;
        }
        
        if ( $currentUser->hasRole( 'ROLE_SUPER_ADMIN' ) || $currentUser->getAllowedRoles()->isEmpty() ) {
            return true;
        }
        
        if ( $currentUser->getAllowedRoles()->contains( $user->topRole() ) ) {
            return true;
        }
        
        return false;
    }
}