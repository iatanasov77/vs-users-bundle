<?php namespace Vankosoft\UsersBundle\Model;

use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserNotificationInterface;

class UserNotification implements UserNotificationInterface
{
    /** @var integer */
    protected $id;

    /**
     * Relation to the User entity
     *
     * @var UserInterface
     */
    protected $user;

    /** @var string */
    protected $notificationFrom;
    
    /** @var string */
    protected $notification;
    
    /** @var string */
    protected $notificationBody;

    /** @var \DateTimeInterface */
    protected $date;
    
    /** @var bool */
    protected $readed;

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    public function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }

    public function setDate($date)
    {
        $this->date = $date;
        
        return $this;
    }
    
    public function setNotificationFrom($notificationFrom)
    {
        $this->notificationFrom = $notificationFrom;
        
        return $this;
    }
    
    public function getNotificationFrom()
    {
        return $this->notificationFrom;
    }
    
    public function setNotification($notification)
    {
        $this->notification = $notification;
        
        return $this;
    }

    public function getNotification()
    {
        return $this->notification;
    }
    
    public function setNotificationBody($notificationBody)
    {
        $this->notificationBody = $notificationBody;
        
        return $this;
    }
    
    public function getNotificationBody()
    {
        return $this->notificationBody;
    }
    
    public function getReaded()
    {
        return $this->readed;
    }
    
    public function setReaded( $readed ): self
    {
        $this->readed = (bool) $readed;
        
        return $this;
    }
    
    public function isReaded()
    {
        return $this->readed;
    }
}
