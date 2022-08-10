<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

trait GuardAuthenticatorTrait
{
    public function getCredentials( Request $request )
    {
        $credentials = [
            'username'      => $request->request->get( '_username' ),
            'password'      => $request->request->get( '_password' ),
            'csrf_token'    => $request->request->get( '_csrf_token' ),
        ];
        
        return $credentials;
    }
    
    public function getUser( $credentials, UserProviderInterface $userProvider )
    {
        $token = new CsrfToken( 'authenticate', $credentials['csrf_token'] );
        if ( ! $this->csrfTokenManager->isTokenValid( $token ) ) {
            throw new InvalidCsrfTokenException();
        }
        
        $user = $this->userRepository->findOneBy( ['username' => $credentials['username']] );
        
        if ( ! $user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException( 'Username could not be found.' );
        }
        
        return $user;
    }
    
    public function checkCredentials( $credentials, UserInterface $user )
    {
        $encoder    = $this->encoderFactory->getPasswordHasher( $user );
        
        return $encoder->verify( $user->getPassword(), $credentials['password'], $user->getSalt() );
    }
    
    public function supportsRememberMe()
    {
        return false;
    }
}
