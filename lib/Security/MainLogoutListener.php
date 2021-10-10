<?php namespace VS\UsersBundle\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\HttpUtils;

/*
 * https://symfony.com/blog/new-in-symfony-5-1-simpler-logout-customization
 * https://github.com/symfony/symfony/issues/37292
 */
final class MainLogoutListener implements EventSubscriberInterface
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
    
    public function onLogout( LogoutEvent $event )
    {
        $response   = $this->httpUtils->createRedirectResponse( $event->getRequest(), $this->targetUrl );
        $response->headers->clearCookie( 'api_token' );
        
        $event->setResponse( $response );
    }

    public static function getSubscribedEvents() : array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}
