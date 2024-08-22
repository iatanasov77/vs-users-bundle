<?php namespace Vankosoft\UsersBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface UserNotificationInterface extends ResourceInterface
{
    /** @return integer */
    public function getId();
    
    /** @return UserInterface */
    public function getUser();
    
    /** @return \DateTimeInterface */
    public function getDate();
    
    /** @return string */
    public function getNotification();
    
    /** @return string */
    public function getNotificationBody();
    
    /** @return bool */
    public function getReaded();
}
