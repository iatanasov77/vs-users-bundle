<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Repository\UsersRepositoryInterface;

use Vankosoft\UsersBundle\Security\ApiManager;

class ApiLoginController extends AbstractController
{
    /** @var ApiManager */
    protected $apiManager;
    
    /** @var UsersRepositoryInterface */
    protected $usersRepository;
    
    public function __construct( ApiManager $apiManager, UsersRepositoryInterface $usersRepository )
    {
        $this->apiManager       = $apiManager;
        $this->usersRepository  = $usersRepository;
    }
    
    public function getLoggedUser( Request $request ): Response
    {
        $token  = $this->apiManager->getToken();
        $user   = $this->usersRepository->findOneBy( ['username' => $token['username']] );
        
        $data   = [
            'tokenCreatedTimestamp' => $token['iat'],
            'tokenExpiredTimestamp' => $token['exp'],
            'user'                  => [
                'username'  => $user->getUsername(),
                'email'     => $user->getEmail(),
                'firstName' => $user->getInfo()->getFirstName(),
                'lastName'  => $user->getInfo()->getLastName(),
            ]
        ];
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data
        ]);
    }

    


}
