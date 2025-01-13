<?php namespace Vankosoft\UsersBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use Sylius\Component\Resource\Factory\FactoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

class ResetPasswordRequestRepository extends EntityRepository implements RepositoryInterface, ResetPasswordRequestRepositoryInterface
{
    use ResourceRepositoryTrait;
    use ResetPasswordRequestRepositoryTrait;
    
    /** @var FactoryInterface */
    protected $requestFactory;
    
    public function setRequestFactory( FactoryInterface $requestFactory )
    {
        $this->requestFactory   = $requestFactory;
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
        if ( ! $this->requestFactory ) {
            throw new \Exception( 'ResetPasswordRequestRepository need to be initialized !!!' );
        }
        
        $resetPasswordRequest   = $this->requestFactory->createNew();
        $resetPasswordRequest->initialize( $expiresAt, $selector, $hashedToken, $user );
        
        return $resetPasswordRequest;
    }
}
