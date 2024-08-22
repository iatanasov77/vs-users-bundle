<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Component\UserException;

class UsersActivitiesController extends AbstractController
{
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var SecurityBridge */
    protected $securityBridge;
    
    public function __construct(
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge
    ) {
        $this->doctrine                 = $doctrine;
        $this->securityBridge           = $securityBridge;
    }
    
    public function clearAll( Request $request ): Response
    {
        $user   = $this->securityBridge->getUser();
        $userIsValid    = ( $user instanceof UserInterface );
        $hasError       = ! $userIsValid;
        
        if ( ! $hasError ) {
            $em = $this->doctrine->getManager();
            foreach ( $user->getNotifications() as $not ) {
                $em->remove( $not );
            }
            $em->flush();
        }
        
        if( $request->isXmlHttpRequest() ) {
            return new JsonResponse([
                'status'    => $hasError ? Status::STATUS_ERROR : Status::STATUS_OK,
                'message'   => $hasError ? 'Invalid User !!!' : 'User is Valid !!!',
            ]);
        } else {
            if ( $hasError ) {
                throw new UserException( 'Invalid User !!!' );
            }
            
            return $this->redirectToRoute( 'vs_users_profile_show' );
        }
    }
}