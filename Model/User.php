<?php namespace VS\UsersBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class User implements UserInterface, \ArrayAccess
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
    protected $apiToken;
    
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
     * @var Collection|UserRole[]
     * 
     * https://symfony.com/doc/current/security.html#hierarchical-roles
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
    protected $firstName    = 'NOT_EDITED_YET';
    
    /**
     * @var string
     */
    protected $lastName     = 'NOT_EDITED_YET';
    
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
    protected $verified;
    
    /**
     * @var bool
     */
    protected $enabled;
    
    /** @var Collection|UserActivity[] */
    protected $activities;
    
    /** @var Collection|UserNotification[] */
    protected $notifications;
    
    public function __construct()
    {
        $this->roles            = new ArrayCollection();
        $this->activities       = new ArrayCollection();
        $this->notifications    = new ArrayCollection();
    }
    
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
    
    public function getApiToken()
    {
        return $this->apiToken;
    }
    
    public function setApiToken( $apiToken ) : self
    {
        $this->apiToken = $apiToken;
        
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
    
    public function setVerified( $verified ) : self
    {
        $this->verified = (bool) $verified;
        
        return $this;
    }
    
    public function isVerified()
    {
        return $this->verified;
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
    
    /**
     * @return Collection|UserRole[]
     */
    public function getRoles()
    {
        return $this->roles;
        
        /*
        $roles = $this->roles;
        
        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;
        
        return array_unique( $roles );
        */
    }
    
    public function setRoles( $roles ) : self
    {
        $this->roles    = $roles;
        
        return $this;
    }
    
    public function hasRole( $role )
    {
        return in_array( strtoupper( $role ), $this->getRoles(), true );
    }
    
    public function addRole( UserRoleInterface $role ) : self
    {
        if ( ! $this->roles->contains( $role ) ) {
            $this->roles[] = $role;
        }
        
        return $this;
    }
    
    public function removeRole( UserRoleInterface $role ) : self
    {
        if ( $this->roles->contains( $role ) ) {
            $this->roles->removeElement( $role );
        }
        
        return $this;
    }
    
    public function eraseCredentials()
    {
        //$this->plainPassword = null;
    }
    
    public function getActivities() : Collection
    {
        return $this->activities;
    }
    
    public function getNotifications() : Collection
    {
        return $this->notifications;
    }
    
    public function getUserIdentifier()
    {
        return $this->username;
    }
    
    public function offsetSet( $offset, $value )
    {
        
    }
    
    public function offsetUnset( $offset )
    {
        
    }
    
    public function offsetExists( $offset )
    {
        return isset( $this->$offset );
    }
    
    public function offsetGet($offset)
    {
        return isset( $this->$offset ) ? $this->$offset : null;
    }
}
