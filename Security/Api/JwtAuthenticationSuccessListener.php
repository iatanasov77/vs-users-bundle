<?php namespace Vankosoft\UsersBundle\Security\Api;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Doctrine\ORM\EntityManager;
use Vankosoft\UsersBundle\Model\UserInterface;

/**
 * Manual: https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/2-data-customization.html#events-authentication-success-adding-public-data-to-the-jwt-response
 */
class JwtAuthenticationSuccessListener
{
    /** @var EntityManager */
    private $entityManager;
    
    public function __construct( EntityManager $entityManager )
    {
        $this->entityManager    = $entityManager;
    }
        
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccess( AuthenticationSuccessEvent $event ): void
    {
        $data = $event->getData();
        $user = $event->getUser();
        
        if ( ! $user instanceof UserInterface ) {
            return;
        }
        
        $user->setLastLogin( new \DateTime() );
        $this->entityManager->persist( $user );
        $this->entityManager->flush();
        
        //$this->modifyResponse( $user, $event );
    }
    
    private function modifyResponse( UserInterface $user,  AuthenticationSuccessEvent &$event ): void
    {
        $data['data'] = [
            'roles' => $user->getRoles(),
        ];
        
        $event->setData( $data );
    }
}