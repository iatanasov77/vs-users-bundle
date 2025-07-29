<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

/**
 * Login with a Token Not With Form ;)
 */
class SuperAdminAccessTokenAuthenticator implements AuthenticatorInterface
{
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var UserManager */
    protected $userManager;
    
    /** @var RepositoryInterface */
    protected $usersRepository;
    
    /** @var FactoryInterface */
    protected $usersFactory;
    
    public function __construct(
        ManagerRegistry $doctrine,
        UserManager $userManager,
        RepositoryInterface $usersRepository,
        FactoryInterface $usersFactory
    ) {
        $this->doctrine         = $doctrine;
        $this->userManager      = $userManager;
        $this->usersRepository  = $usersRepository;
        $this->usersFactory     = $usersFactory;
    }
    
    public function supports( Request $request ): ?bool
    {
        return $request->query->get( 'token' ) === '123456789' ? true : false;
    }
    
    public function authenticate( Request $request ): Passport
    {
        $apiToken   = $request->query->get( 'token' );
        $user       = $this->usersRepository->find( 1 );
        
        // Use anonymous class which implements UserInterface.
        return new SelfValidatingPassport( new UserBadge( $apiToken, fn() => $user ) );
    }
    
    public function createToken( Passport $passport, string $firewallName ): TokenInterface
    {
        return new PostAuthenticationToken( $passport->getUser(), $firewallName, $passport->getUser()->getRoles() );
    }
    
    public function onAuthenticationSuccess( Request $request, TokenInterface $token, string $firewallName ): ?Response
    {
        return null;
    }
    
    public function onAuthenticationFailure( Request $request, AuthenticationException $exception ): ?Response
    {
        return null;
    }
}
