<?php namespace VS\UsersBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

use VS\ApplicationBundle\Component\Context\ApplicationContextInterface;
use VS\UsersBundle\Model\UserInterface;

class ApplicationVoter implements VoterInterface
{
    private $security;
    
    private ApplicationContextInterface $applicationContext;
    
    public function __construct(
        Security $security,
        ApplicationContextInterface $applicationContext
    ) {
            $this->security             = $security;
            $this->applicationContext   = $applicationContext;
    }
    
    /**
     * {@inheritdoc}
     */
    public function vote( TokenInterface $token, $subject, array $attributes )
    {
        if ( ! $subject instanceof Request ) {
            return self::ACCESS_ABSTAIN;
        }
        $user   = $token->getUser();
        
        if ( ! $user instanceof UserInterface ) {
            return self::ACCESS_DENIED;
        }
        
        if ( $user->hasRole( 'ROLE_SUPER_ADMIN' ) ) {
            return self::ACCESS_GRANTED;
        }
        
        if ( ! $user->getApplications()->isEmpty() ) {
            return self::ACCESS_ABSTAIN;
        }
        
        $application    = $this->applicationContext->getApplication();
        $uri            = $subject->getUri(); // $uri may be needed sometimes
        foreach ( $user->getApplications() as $userApplication ) {
            if ( $userApplication == $application ) {
                return self::ACCESS_GRANTED;
            }
        }
        
        return self::ACCESS_DENIED;
    }
}
