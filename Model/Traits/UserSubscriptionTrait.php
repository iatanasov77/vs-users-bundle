<?php namespace Vankosoft\UsersBundle\Model\Traits;

trait UserSubscriptionTrait
{
    /**
     * @ORM\OneToOne(targetEntity="Vankosoft\UsersBundle\Entity\UserSubscription", inversedBy="user")
     * @ORM\JoinColumn(name="subscriptionId", referencedColumnName="id")
     */
    protected $subscription;
    
    public function getSubscription()
    {
        return $this->subscription;
    }
    
    public function setSubscription(&$subscription)
    {
        $subscription->setUser($this);
        $this->subscription = $subscription;
        
        return $this;
    }
}
