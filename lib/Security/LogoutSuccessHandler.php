<?php namespace VS\UsersBundle\Security;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;

/*
 * https://chrisguitarguy.com/2019/08/06/symfony-logout-handlers-vs-logout-success-handlers/
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    protected $httpUtils;
    protected $targetUrl;
    
    public function __construct( HttpUtils $httpUtils, string $targetUrl = '/' )
    {
        $this->httpUtils = $httpUtils;
        $this->targetUrl = $targetUrl;
    }
    
    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess( Request $request )
    {
        $response   = $this->httpUtils->createRedirectResponse( $request, $this->targetUrl );
        $response->headers->clearCookie( 'api_token' );
        
        return $response;
    }
}
