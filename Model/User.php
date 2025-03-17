<?php namespace Vankosoft\UsersBundle\Model;

use Doctrine\Common\Comparable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserRoleInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserNotificationInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserActivityInterface;

use Vankosoft\UsersBundle\Model\Traits\UserPasswordTrait;
use Vankosoft\UsersBundle\Model\Traits\UserRolesArrayTrait;
use Vankosoft\UsersBundle\Model\Traits\UserRolesCollectionTrait;
use Vankosoft\UsersBundle\Model\Traits\UserApplicationsTrait;

class User implements UserInterface, Comparable
{
    use TimestampableTrait;
    use UserPasswordTrait;
    use UserRolesArrayTrait;
    use UserRolesCollectionTrait;
    use UserApplicationsTrait;
    
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
        $this->rolesCollection  = new ArrayCollection(); // Cannot write this in trait Constructor
        $this->activities       = new ArrayCollection();
        $this->notifications    = new ArrayCollection();
        $this->applications     = new ArrayCollection();
        $this->allowedRoles     = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * For Backward Compatibility Create 2 Roles Fields <rolesArray and rolesCollection>
     * You can choose which to use
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->getRolesFromArray();
        
        /* EXAMPLE To Use RoleCollection */
        //return $this->getRolesFromCollection();
    }
    
    public function hasRole( string $role ): bool
    {
        return in_array( strtoupper( $role ), $this->getRoles(), true );
    }
    
    public function getInfo()
    {
        return $this->info;
    }
    
    public function setInfo( UserInfo $info ): self
    {
        $this->info = $info;
        
        return $this;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername( $username ): self
    {
        $this->username = $username;
        
        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail( $email ): self
    {
        $this->email = $email;
        
        return $this;
    }
    
    public function getPreferedLocale()
    {
        return $this->preferedLocale;
    }
    
    public function setPreferedLocale( $preferedLocale ): self
    {
        $this->preferedLocale   = $preferedLocale;
        
        return $this;
    }
    
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
    
    public function setLastLogin( \DateTime $time = null ): self
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
    
    public function setVerified( $verified ): self
    {
        $this->verified = (bool) $verified;
        
        return $this;
    }
    
    public function isVerified()
    {
        return $this->verified;
    }
    
    public function setEnabled( $boolean ): self
    {
        $this->enabled = (bool) $boolean;
        
        return $this;
    }
    
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    public function getActivities(): Collection
    {
        return $this->activities;
    }
    
    public function addActivity( UserActivityInterface $activity ): self
    {
        $this->activities[] = $activity;
        
        return $this;
    }
    
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }
    
    public function getUnreadedNotifications(): Collection
    {
        return $this->getNotifications()->filter( function( UserNotificationInterface $notification )
        {
            return ! $notification->isReaded();
        });
    }
    
    public function getUserIdentifier(): string
    {
        return $this->username;
    }
    
    /**
     * {@inheritDoc}
     * @see \Doctrine\Common\Comparable::compareTo($other)
     */
    public function compareTo($other): int
    {
        if ( ! ( $other instanceof UserInterface ) ) {
            throw new \Exception( 'Vankosoft User can to be Compared with other Vankosoft User Objects !!!' );
        }
        
        $compareValue   = 1;
        foreach ( $this->rolesCollection as $role ) {
            if ( $compareValue === -1 ) {
                break;
            }
            
            foreach ( $other->getRolesCollection() as $otherRole ) {
                if ( $compareValue === -1 ) {
                    break;
                }
                
                $compareValue   = $role->compareTo( $otherRole );
            }
        }
        
        return $compareValue;
    }
    
    /**
     * Get Top Role of This User
     * 
     * @throws \Exception
     * @return UserRoleInterface
     */
    public function topRole(): UserRoleInterface
    {
        $topRole    = $this->rolesCollection->first();
        foreach ( $this->rolesCollection as $role ) {
            if ( $role->compareTo( $topRole ) == 1 ) {
                $topRole    = $role;
            }
        }
        
        return $topRole;
    }
}
