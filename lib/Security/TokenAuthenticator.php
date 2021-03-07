<?php namespace VS\UsersBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

use VS\UsersBundle\Repository\UsersRepository;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $userRepository;
    
    public function __construct( UsersRepository $userRepository )
    {
        $this->userRepository   = $userRepository;
    }
    
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports( Request $request )
    {
        return true;
    }
    
    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials( Request $request )
    {
        return [
            'token' => $request->cookies->get( 'api_token' ),
        ];
    }
    
    public function getUser( $credentials, UserProviderInterface $userProvider )
    {
        $token = $credentials['token'];
        
        if ( null === $token ) {
            return;
        }
        
        // if a User object, checkCredentials() is called
        return $this->userRepository->findOneBy( ['apiToken' => $token] );
    }
    
    public function checkCredentials( $credentials, UserInterface $user )
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        
        // return true to cause authentication success
        return true;
    }
    
    public function onAuthenticationSuccess( Request $request, TokenInterface $token, $providerKey )
    {
        // on success, let the request continue
        return null;
    }
    
    public function onAuthenticationFailure( Request $request, AuthenticationException $exception )
    {
        // @NOTE: Remove this if you want to fail authentication
        return null;
        
        
        $data = [
            'message' => strtr( $exception->getMessageKey(), $exception->getMessageData() )
            
            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];
        
        return new JsonResponse( $data, Response::HTTP_FORBIDDEN );
    }
    
    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start( Request $request, AuthenticationException $authException = null )
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];
        
        return new JsonResponse( $data, Response::HTTP_UNAUTHORIZED );
    }
    
    public function supportsRememberMe()
    {
        return false;
    }
}
