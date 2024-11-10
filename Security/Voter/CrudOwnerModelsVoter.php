<?php namespace Vankosoft\UsersBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

class CrudOwnerModelsVoter extends CrudVoter
{
    private ApplicationContextInterface $applicationContext;
    
    /** @var Collection */
    private $ownerModels;
    
    private $authorizationCheker;
    
    /** @var ContainerInterface */
    private $container;
    
    public function __construct(
        ApplicationContextInterface $applicationContext,
        array $ownerModels,
        ContainerInterface $container
    ) {
        $this->applicationContext   = $applicationContext;
        //$this->ownerModels          = new ArrayCollection( $ownerModels );
        $this->ownerModels          = $ownerModels;
        $this->container            = $container;
    }
    
    protected function supports( string $attribute, $subject ): bool
    {
        return is_object( $subject ) && in_array( get_class( $subject ), $this->ownerModels );
    }
    
    /**
     * You can use security classes to check granted
     * ===============================================
     * $securityContext        = $this->container->get( 'security.context' );
     * $securityContext->isGranted( $attribute, $subject );
     * 
     * $authorizationChecker   = $this->container->get( 'security.authorization_checker' );
     * $authorizationChecker->isGranted( $attribute, $subject );
     */
    protected function voteOnAttribute( string $attribute, $subject, TokenInterface $token ): bool
    {        
        $user = $token->getUser();
        
        if ( ! $user instanceof UserInterface ) {
            // the user must be logged in; if not, deny access
            return false;
        }
        
        // you know $subject is an Entity object, thanks to `supports()`
        return $user === $subject->getOwner();
    }
}
