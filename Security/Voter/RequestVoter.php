<?php namespace Vankosoft\UsersBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\Request;

// Symfony\Component\Security\Core\Security implements AuthorizationCheckerInterface
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

class RequestVoter implements VoterInterface
{
    private $security;
    
    private ApplicationContextInterface $applicationContext;
    
    private $roleHierarchy;
    
    public function __construct(
        ApplicationContextInterface $applicationContext,
        ?AuthorizationCheckerInterface $security,
        ?RoleHierarchyInterface $roleHierarchy
     ) {
            $this->applicationContext   = $applicationContext;
            $this->security             = $security;
            $this->roleHierarchy        = $roleHierarchy;
    }
    
    /**
     * {@inheritdoc}
     */
    public function vote( TokenInterface $token, $subject, array $attributes ): int
    {
        // Not Used For Now
        return self::ACCESS_ABSTAIN;
        
        if ( ! $subject instanceof Request ) {
            return self::ACCESS_ABSTAIN;
        }
        
        $user           = $token->getUser();
        $uri            = $subject->getUri();
        $reachableRoles = $this->roleHierarchy->getReachableRoleNames( $token->getRoles() );
    }
}
