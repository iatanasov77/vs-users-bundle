<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Status;

use Vankosoft\UsersBundle\Security\ApiManager;

class ApiLoginController extends AbstractController
{
    /** @var ApiManager */
    protected $apiManager;
    
    public function __construct( ApiManager $apiManager )
    {
        $this->apiManager   = $apiManager;
    }
    
    public function getLoggedUser( Request $request ): Response
    {
        /*
        $data   = [];
        foreach ( $this->applicationRepository->findAll() as $application ) {
            $data[] = [
                'code'      => $application->getCode(),
                'title'     => $application->getTitle(),
                'hostname'  => $application->getHostname()
            ];
        }
        */
        
        $user   = $this->apiManager->getToken()->getUser();
        var_dump( $user ); die;
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data
        ]);
    }

    


}
