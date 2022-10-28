<?php namespace Vankosoft\UsersBundle\Model\Traits;


trait UserPasswordTrait
{
    /**
     * The salt to use for hashing.
     *
     * @var string
     */
    protected $salt;
    
    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;
    
    /**
     * @var \DateTime|null
     */
    protected $passwordRequestedAt;
    
    /**
     * Required by Symfony\Component\Security\Core\User\UserInterface
     */
    public function eraseCredentials()
    {
        //$this->plainPassword = null;
    }
    
    public function getSalt()
    {
        return $this->salt;
    }
    
    public function setSalt( $salt ) : self
    {
        $this->salt = $salt;
        
        return $this;
    }
    
    public function getPassword(): ?string
    {
        return $this->password;
    }
    
    public function setPassword( $password ) : self
    {
        $this->password = $password;
        
        return $this;
    }
    
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }
    
    public function setPasswordRequestedAt( \DateTime $date = null )
    {
        $this->passwordRequestedAt = $date;
        
        return $this;
    }
}
