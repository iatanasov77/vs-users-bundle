<?php namespace Vankosoft\UsersBundle\Component;

use Doctrine\ORM\EntityManager;
use Sylius\Component\Resource\Factory\Factory;
use Vankosoft\UsersBundle\Repository\UserRolesRepository;

class UserNotifications
{
    /** @var UserRolesRepository */
    private $userRolesRepository;
    
    /** @var Factory */
    private $userNotificationsFactory;
    
    /** @var EntityManager */
    private $entityManager;
    
    public function __construct(
        UserRolesRepository $userRolesRepository,
        Factory $userNotificationsFactory,
        EntityManager $entityManager
    ) {
        $this->userRolesRepository      = $userRolesRepository;
        $this->userNotificationsFactory = $userNotificationsFactory;
        $this->entityManager            = $entityManager;
    }
    
    public function sentNotificationByRole( string $role, string $from, string $notification, string $notificationBody = null )
    {
        $role   = $this->userRolesRepository->findByTaxonCode( $role );
        if ( ! $role ) {
            return;
        }
        
        foreach ( $role->getUsers() as $user ) {
            $oNotification  = $this->userNotificationsFactory->createNew();
            $oNotification->setNotificationFrom( $from );
            $oNotification->setNotification( $notification );
            $oNotification->setNotificationBody( $notificationBody );
            $oNotification->setUser( $user );
            
            $this->entityManager->persist( $oNotification );
        }
        
        $this->entityManager->flush();
    }
}
