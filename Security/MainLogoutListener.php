<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\HttpUtils;

/*
 * https://symfony.com/blog/new-in-symfony-5-1-simpler-logout-customization
 * https://stackoverflow.com/questions/60998790/symfony-5confirmation-message-after-logout
 */
final class MainLogoutListener
{
    /** @var HttpUtils */
    protected $httpUtils;
    
    /** @var string */
    protected $targetUrl;
    
    public function __construct( HttpUtils $httpUtils, string $targetUrl = '/' )
    {
        $this->httpUtils = $httpUtils;
        $this->targetUrl = $targetUrl;
    }
    
    /**
     * @param LogoutEvent $logoutEvent
     * @return void
     */
    public function onSymfonyComponentSecurityHttpEventLogoutEvent( LogoutEvent $logoutEvent ) : void
    {
        $response   = $this->httpUtils->createRedirectResponse( $logoutEvent->getRequest(), $this->targetUrl );
        $response->headers->clearCookie( 'api_token' );
        
        $logoutEvent->setResponse( $response );
    }
}
