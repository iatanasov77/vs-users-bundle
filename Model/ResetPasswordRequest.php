<?php namespace Vankosoft\UsersBundle\Model;

use Vankosoft\UsersBundle\Model\Interfaces\ResetPasswordRequestInterface;

class ResetPasswordRequest implements ResetPasswordRequestInterface
{   
    /**
     * @var mixed
     */
    protected $id;
    
    /**
     * Relation to the User entity
     */
    protected $user;
    
    /**
     * @var mixed
     */
    protected $selector;
    
    /**
     * @var mixed
     */
    protected $hashedToken;
    
    /**
     * @var mixed
     */
    protected $requestedAt;
    
    /**
     * @var mixed
     */
    protected $expiresAt;
    
    public function initialize(
        \DateTimeInterface $expiresAt,
        string $selector, 
        string $hashedToken,
        object $user
    ) {
        $this->requestedAt  = new \DateTimeImmutable('now');
        $this->expiresAt    = $expiresAt;
        $this->selector     = $selector;
        $this->hashedToken  = $hashedToken;
        $this->user         = $user;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUser() : object
    {
        return $this->user;
    }
    
    public function getRequestedAt(): \DateTimeInterface
    {
        return $this->requestedAt;
    }
    
    public function isExpired(): bool
    {
        return $this->expiresAt->getTimestamp() <= \time();
    }
    
    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }
    
    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }
}