<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;

use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;

use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

use Vankosoft\UsersBundle\Repository\UsersRepository;

/**
 *  Needed For RegistrationController:verify Action
 *  ================================================
 *  Examples: https://stackoverflow.com/questions/9550079/how-to-programmatically-login-authenticate-a-user
 *  
 *  There is example for Symfony 6 Also
 */
class AnotherLoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    
    public function __construct (
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        PasswordHasherFactoryInterface $encoderFactory,
        UsersRepository $userRepository,
        array $params
    ) {
            $this->urlGenerator     = $urlGenerator;
            $this->csrfTokenManager = $csrfTokenManager;
            $this->encoderFactory   = $encoderFactory;
            $this->userRepository   = $userRepository;
            $this->params           = $params;
    }
    
    public function supports( Request $request ): bool
    {
        return $this->params['loginRoute'] === $request->attributes->get( '_route' ) && $request->isMethod( 'POST' );
    }
    
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
    
    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword( $credentials ): ?string
    {
        return $credentials['password'];
    }
    
    public function authenticate( Request $request ): Passport
    {
        $password   = $request->request->get( '_password' );
        $username   = $request->request->get( '_username' );
        $csrfToken  = $request->request->get( '_csrf_token' );
        
        // ... validate no parameter is empty
        
        return new Passport(
            new UserBadge( $username ),
            new PasswordCredentials( $password ),
            [new CsrfTokenBadge( 'authenticate', $csrfToken )]
        );
    }
    
    public function onAuthenticationSuccess( Request $request, TokenInterface $token, string $firewallName ): ?Response
    {
        return new RedirectResponse( $this->urlGenerator->generate( $this->params['redirectAfterLogin'] ) );
    }
    
    protected function getLoginUrl( Request $request ): string
    {
        return $this->urlGenerator->generate( $this->params['loginRoute'] );
    }
}
