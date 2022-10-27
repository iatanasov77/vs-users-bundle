<?php namespace Vankosoft\UsersBundle\Security\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;

use Vankosoft\UsersBundle\Repository\UsersRepository;

/**
 * https://symfony.com/doc/current/security/authenticator_manager.html
 * 
 * NOT FINISHED AND MAY BE NOT NEEDED ANYMORE
 */
class ApiKeyAuthenticator extends AbstractAuthenticator
{
    /** @var UsersRepository */
    private $userRepository;
    
    public function __construct( UsersRepository $userRepository )
    {
        $this->userRepository   = $userRepository;
    }
    
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports( Request $request ) : ?bool
    {
        return $request->headers->has( 'X-AUTH-TOKEN' );
    }
    
    public function authenticate( Request $request ): PassportInterface
    {
        $email      = 'email@example.com';
        //$apiToken = $request->cookies->get( 'api_token' ),
        $apiToken   = $request->headers->get( 'X-AUTH-TOKEN' );
        
        if ( null === $apiToken ) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException( 'No API token provided' );
        }
        
        /**
         * If you don’t need any credentials to be checked (e.g. when using API tokens), you can use the
         * Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport. This class only 
         * requires a UserBadge object and optionally Passport Badges.
         */
        //return new SelfValidatingPassport( new UserBadge( $apiToken ) );
        
        return new Passport( new UserBadge( $email ), new CustomCredentials(
            // If this function returns anything else than `true`, the credentials
            // are marked as invalid.
            // The $credentials parameter is equal to the next argument of this class
            function ( $credentials, UserInterface $user ) {
                return $user->getApiToken() === $credentials;
            },
            
            // The custom credentials
            $apiToken
        ));
    }
    
    public function onAuthenticationSuccess( Request $request, TokenInterface $token, string $firewallName ): ?Response
    {
        // on success, let the request continue
        return null;
    }
    
    public function onAuthenticationFailure( Request $request, AuthenticationException $exception ): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr( $exception->getMessageKey(), $exception->getMessageData() )
            
            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];
        
        return new JsonResponse( $data, Response::HTTP_UNAUTHORIZED );
    }
    
    private function getUser( $credentials, UserProviderInterface $userProvider )
    {
        $token = $credentials['token'];
        
        if ( null === $token ) {
            return;
        }
        
        // if a User object, checkCredentials() is called
        return $this->userRepository->findOneBy( ['apiToken' => $token] );
    }
}
