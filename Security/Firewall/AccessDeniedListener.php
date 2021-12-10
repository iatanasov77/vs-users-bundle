<?php namespace VS\UsersBundle\Security\Firewall;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccessDeniedListener implements EventSubscriberInterface
{
    private $urlGenerator;
    
    private $params;
    
    public function __construct (
        UrlGeneratorInterface $urlGenerator,
        array $params
    ) {
            $this->urlGenerator     = $urlGenerator;
            $this->params           = $params;
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            // the priority must be greater than the Security HTTP
            // ExceptionListener, to make sure it's called before
            // the default exception listener
            KernelEvents::EXCEPTION => ['onKernelException', 2],
        ];
    }
    
    public function onKernelException( ExceptionEvent $event ): void
    {
        $exception  = $event->getThrowable();
        if ( ! $exception instanceof AccessDeniedException ) {
            return;
        }
        
        // ... perform some action (e.g. logging)
        
        // optionally set the custom response
        $event->setResponse( new RedirectResponse( $this->urlGenerator->generate( $this->params['loginRoute'] ) ) );
        
        // or stop propagation (prevents the next exception listeners from being called)
        //$event->stopPropagation();
    }
}
