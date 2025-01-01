<?php namespace Vankosoft\UsersBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

class ApplicationVoter implements VoterInterface
{
    private $security;
    
    private ApplicationContextInterface $applicationContext;
    
    public function __construct(
        ApplicationContextInterface $applicationContext,
        Security $security  = null
    ) {
            $this->applicationContext   = $applicationContext;
            $this->security             = $security;
    }
    
    /**
     * {@inheritdoc}
     */
    public function vote( TokenInterface $token, $subject, array $attributes ): int
    {
        $user   = $token->getUser();
        
        if ( ! $user instanceof UserInterface ) {
            return self::ACCESS_DENIED;
        }
        
        if ( $user->hasRole( 'ROLE_SUPER_ADMIN' ) ) {
            return self::ACCESS_GRANTED;
        }
        
        if ( $user->getApplications()->isEmpty() ) {
            return self::ACCESS_ABSTAIN;
        }
        
        $application    = $this->applicationContext->getApplication();
        foreach ( $user->getApplications() as $userApplication ) {
            if ( $userApplication == $application ) {
                return self::ACCESS_GRANTED;
            }
        }
        
        return self::ACCESS_DENIED;
    }
}
