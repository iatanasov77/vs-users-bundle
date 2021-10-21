<?php namespace VS\UsersBundle\Model;

use Doctrine\ORM\Mapping as ORM;

class UserWithAllRelations // extends Model\UserWithAllRelations
{
    /**
     * FOR USERS WITH SUBSCRIPTION DERIVE THIS ENTITY AND ADD USING
     * THE TRAIT `VS\UsersBundle\Entity\Traits\UserSubscriptionTrait` FORM THIS BUNDLE
     */
    //use Traits\UserSubscriptionTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="VS\UsersBundle\Entity\UserInfo", cascade={"persist"})
     * @ORM\JoinColumn(name="user_info_id", referencedColumnName="id")
     */
    protected $userInfo;

    /**
     * @ORM\OneToOne(targetEntity="VS\UsersBundle\Model\SubscriptionInterface", inversedBy="user")
     * @ORM\JoinColumn(name="subscriptionId", referencedColumnName="id")
     */
    protected $subscription;
    
    /**
     * @ORM\OneToMany(targetEntity="VS\UsersBundle\Entity\UserActivity", mappedBy="user")
     */
    protected $activities;
    
    /**
     * @ORM\OneToMany(targetEntity="VS\UsersBundle\Entity\UserNotification", mappedBy="user")
     */
    protected $notifications;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    // She ti eba i formite
    public function __get( $var )
    {
        if ( property_exists ( 'VS\UsersBundle\Entity\UserInfo' , $var ) ) {
            return $this->userInfo ? $this->userInfo->$var : null;
        }
    }
    
    public function getUserInfo()
    {
        return $this->userInfo;
    }
    
    public function setUserInfo( $userInfo )
    {
        $this->userInfo = $userInfo;
        
        return $this;
    }
    
    /**
     * @return Collection|UserActivity[]
     */
    public function getActivities()
    {
        return $this->activities;
    }
    
    /**
     * @return Collection|UserActivity[]
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    public function __toString()
    {
        return ( string ) $this->userInfo ? $this->userInfo->getFullName() : '';
    }
}
