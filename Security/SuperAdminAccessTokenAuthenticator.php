<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

class SuperAdminAccessTokenAuthenticator implements AuthenticatorInterface
{
    public function supports( Request $request ): ?bool
    {
        return $request->query->get( 'token' ) === '123456789' ? true : false;
    }
    
    public function authenticate( Request $request ): Passport
    {
        // Use anonymous class which implements UserInterface.
        return new SelfValidatingPassport( new UserBadge( $apiToken, fn() => new class implements UserInterface {
            public function getRoles(): array { return ['ROLE_SUPER_ADMIN']; }
            public function eraseCredentials() {}
            public function getUserIdentifier(): string
            {
                return 'fake_admin';
            }
            
            public function hasRole( string $role ): bool { return true; }
            public function getRolesFromArray(): array { return []; }
            public function getRolesFromCollection(): array { return []; }
            public function getActivities(): Collection { return new ArrayCollection(); }
            public function getNotifications(): Collection { return new ArrayCollection(); }
        }));
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
