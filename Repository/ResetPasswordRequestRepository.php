<?php namespace Vankosoft\UsersBundle\Repository;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use Symfony\Contracts\Service\ServiceProviderInterface;

class ResetPasswordRequestRepository extends EntityRepository implements ResetPasswordRequestRepositoryInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ResetPasswordRequestRepositoryTrait;
    
    /**
     * Replace container with service locator
     */
    private $serviceLocator;
    
    public function setServiceLocator( ServiceProviderInterface $serviceLocator )
    {
        $this->serviceLocator   = $serviceLocator;
    }
    
    /**
     * Create a new ResetPasswordRequest object.
     *
     * @param object $user        User entity - typically implements Symfony\Component\Security\Core\User\UserInterface
     * @param string $selector    A non-hashed random string used to fetch a request from persistence
     * @param string $hashedToken The hashed token used to verify a reset request
     */
    public function createResetPasswordRequest(
        object $user,
        \DateTimeInterface $expiresAt,
        string $selector,
        string $hashedToken
    ): ResetPasswordRequestInterface
    {
        if ( ! $this->container && ! $this->serviceLocator ) {
            throw new \Exception( 'ResetPasswordRequestRepository need to be initialized !!!' );
        }
        
        if ( $this->container ) {
            $resetPasswordRequest   = $this->container->get( 'vs_users.factory.reset_password_request' )->createNew();
        } elseif ( $this->serviceLocator ) {
            $resetPasswordRequest   = $this->serviceLocator->get( 'vs_users.factory.reset_password_request' )->createNew();
        }
        
        $resetPasswordRequest->initialize( $expiresAt, $selector, $hashedToken, $user );
        
        return $resetPasswordRequest;
    }
}
