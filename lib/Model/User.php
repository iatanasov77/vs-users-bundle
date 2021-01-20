<?php namespace VS\UsersBundle\Model;

class User implements UserInterface
{
    const ROLE_DEFAULT = "ROLE_DEFAULT";
    
    /**
     * @var mixed
     */
    protected $id;

    /**
     * Relation to the UserInfo entity
     * 
     * @var mixed
     */
    protected $info;
    
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

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
     * @var array
     */
    protected $roles;
    
    /**
     * Prefered locale.
     *
     * @var string|null
     */
    protected $preferedLocale;
    
    /**
     * @var string
     */
    protected $firstName;
    
    /**
     * @var string
     */
    protected $lastName;
    
    /**
     * @var \DateTime|null
     */
    protected $lastLogin;
    
    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string|null
     */
    protected $confirmationToken;
    
    /**
     * @var \DateTime|null
     */
    protected $passwordRequestedAt;
    
    /**
     * @var bool
     */
    protected $enabled;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getInfo()
    {
        return $this->info;
    }
    
    public function setInfo( UserInfo $info ) : self
    {
        $this->info = $info;
        
        return $this;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername( $username ) : self
    {
        $this->username = $username;
        
        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail( $email ) : self
    {
        $this->email = $email;
        
        return $this;
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
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword( $password ) : self
    {
        $this->password = $password;
        
        return $this;
    }
    
    public function getPreferedLocale()
    {
        return $this->preferedLocale;
    }
    
    public function setPreferedLocale( $preferedLocale ) : self
    {
        $this->preferedLocale   = $preferedLocale;
        
        return $this;
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    public function setFirstName( $firstName ) : self
    {
        $this->firstName = $firstName;
        
        return $this;
    }
    
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function setLastName( $lastName ) : self
    {
        $this->lastName = $lastName;
        
        return $this;
    }
    
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
    
    public function setLastLogin( \DateTime $time = null ) : self
    {
        $this->lastLogin = $time;
        
        return $this;
    }
    
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }
    
    public function setConfirmationToken( $confirmationToken )
    {
        $this->confirmationToken = $confirmationToken;
        
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
    
    public function setEnabled( $boolean ) : self
    {
        $this->enabled = (bool) $boolean;
        
        return $this;
    }
    
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    public function getRoles()
    {
        $roles = $this->roles;
        
        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;
        
        return array_unique( $roles );
    }
    
    public function hasRole( $role )
    {
        return in_array( strtoupper( $role ), $this->getRoles(), true );
    }
    
    public function eraseCredentials()
    {
        //$this->plainPassword = null;
    }
}
