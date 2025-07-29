<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\AccessTokenAuthenticator;
use Symfony\Component\Security\Http\AccessToken\QueryAccessTokenExtractor;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

class SuperAdminAccessTokenAuthenticator extends AccessTokenAuthenticator
{
    public function supports( Request $request ): ?bool
    {
        return $request->query->get( 'token' ) === '123456789' ? true : false;
    }
    
    public function authenticate( Request $request ): Passport
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        
        if ( null === $apiToken ) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            //throw new CustomUserMessageAuthenticationException( 'No API token provided' );
        }
        
        // Check token is correct
        if ( $apiToken === $this->expectedApiToken ) {
            // fail authentication with a custom error
            //throw new CustomUserMessageAuthenticationException( 'Invalid API token' );
        }
        
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
}
