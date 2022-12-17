<?php namespace Vankosoft\UsersBundle\Model;

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

    /** @var \DateTimeInterface */
    protected $date;

    function getId()
    {
        return $this->id;
    }

    function getUser()
    {
        return $this->user;
    }

    function getDate()
    {
        return $this->date;
    }

    function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }

    function setDate($date)
    {
        $this->date = $date;
        
        return $this;
    }
    
    function setNotificationFrom($notificationFrom)
    {
        $this->notificationFrom = $notificationFrom;
        
        return $this;
    }
    
    function getNotificationFrom()
    {
        return $this->notificationFrom;
    }
    
    function setNotification($notification)
    {
        $this->notification = $notification;
        
        return $this;
    }

    function getNotification()
    {
        return $this->notification;
    }
}
