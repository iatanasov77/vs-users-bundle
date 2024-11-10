<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordRandomGenerator;

use Doctrine\ORM\EntityManager;

use Vankosoft\UsersBundle\Repository\UsersRepository;
use Vankosoft\UsersBundle\Model\Interfaces\ApiUserInterface;

//class LoginFormAuthenticator extends AbstractAuthenticator
class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    
    /** @var EntityManager */
    private $entityManager;
    
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;
    
    /** @var UsersRepository */
    private $userRepository;
    
    /** @var PasswordHasherFactoryInterface */
    private $encoderFactory;
    
    /** @var TranslatorInterface */
    private $translator;
    
    /** @var ResetPasswordRandomGenerator */
    private $randomGenerator;
    
    /** @var array */
    private $params;
    
    public function __construct (
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        PasswordHasherFactoryInterface $encoderFactory,
        UsersRepository $userRepository,
        EntityManager $entityManager,
        TranslatorInterface $translator,
        ResetPasswordRandomGenerator $generator,
        array $params
    ) {
        $this->urlGenerator     = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->encoderFactory   = $encoderFactory;
        $this->userRepository   = $userRepository;
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
        $this->randomGenerator  = $generator;
        $this->params           = $params;
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
            [
                new CsrfTokenBadge( 'authenticate', $csrfToken ),
                new RememberMeBadge(),
            ]
        );
    }
    
    public function onAuthenticationSuccess( Request $request, TokenInterface $token, string $firewallName ): ?Response
    {
        if ( $request->hasSession() ) {
            $request->getSession()->getFlashBag()
                ->add( 'notice', $this->translator->trans( 'vs_users.security.authentication_success', [], 'VSUsersBundle' ) );
        }
        
        $user   = $token->getUser();
        
        $user->setLastLogin( new \DateTime() );
        if ( $user instanceof ApiUserInterface ) {
            $verifier   = $this->randomGenerator->getRandomAlphaNumStr();
            $expiresAt  = new \DateTimeImmutable( \sprintf( '+%d seconds', 3600 ) );
            
            $user->setApiVerifySiganature( $verifier );
            $user->setApiVerifyExpiresAt( \DateTime::createFromImmutable( $expiresAt ) );
        }
        
        $this->entityManager->persist( $user );
        $this->entityManager->flush();
        
        // on success, let the request continue
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure( Request $request, AuthenticationException $exception ): Response
    {
        if ( $request->hasSession() ) {
            $request->getSession()->set( SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception );
            $request->getSession()->getFlashBag()
                ->add( 'error', $this->translator->trans( 'vs_users.security.authentication_failure', [], 'VSUsersBundle' ) );
        }
        
        $url = $this->getLoginUrl( $request );
        
        return new RedirectResponse( $url );
    }
    
    protected function getLoginUrl( Request $request ): string
    {
        return $this->urlGenerator->generate( $this->params['loginRoute'] );
    }
}
