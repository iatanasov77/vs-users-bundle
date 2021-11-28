<?php namespace VS\UsersBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class User implements UserInterface
{
    use Traits\UserPasswordTrait;
    use Traits\UserRolesArrayTrait;
    use Traits\UserRolesCollectionTrait;
    
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
     * Prefered locale.
     *
     * @var string|null
     */
    protected $preferedLocale;
    
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
    
    public function getPreferedLocale()
    {
        return $this->preferedLocale;
    }
    
    public function setPreferedLocale( $preferedLocale ) : self
    {
        $this->preferedLocale   = $preferedLocale;
        
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
    
    /**
     * For Backward Compatibility Create 2 Roles Fields <rolesArray and rolesCollection>
	 * You can choose which to use
	 * 
     * @return array
     */
    public function getRoles()
    {
        return $this->getRolesFromArray();
        
        /* EXAMPLE To Use RoleCollection */
        //return $this->getRolesFromCollection();
    }
    
    public function getActivities() : Collection
    {
        return $this->activities;
    }
    
    public function getNotifications() : Collection
    {
        return $this->notifications;
    }
}
