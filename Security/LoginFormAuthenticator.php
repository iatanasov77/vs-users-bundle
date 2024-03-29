<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Cookie;

use Doctrine\ORM\EntityManager;

use Vankosoft\UsersBundle\Repository\UsersRepository;

//class LoginFormAuthenticator extends AbstractAuthenticator
class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    
    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    
    private $userRepository;
    private $encoderFactory;
    
    private $params;
    
    public function __construct (
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        PasswordHasherFactoryInterface $encoderFactory,
        UsersRepository $userRepository,
        EntityManager $entityManager,
        array $params
    ) {
        $this->urlGenerator     = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->encoderFactory   = $encoderFactory;
        $this->userRepository   = $userRepository;
        $this->entityManager    = $entityManager;
        $this->params           = $params;
    }
    
    public function authenticate( Request $request ) : Passport
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
    
    public function onAuthenticationSuccess( Request $request, TokenInterface $token, string $firewallName ) : ?Response
    {
        $user   = $token->getUser();
        
        $user->setLastLogin( new \DateTime() );
        $this->entityManager->persist( $user );
        $this->entityManager->flush();
        
        // on success, let the request continue
        return null;
    }
    
    protected function getLoginUrl( Request $request ) : string
    {
        return $this->urlGenerator->generate( $this->params['loginRoute'] );
    }
}
