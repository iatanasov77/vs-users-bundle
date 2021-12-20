<?php namespace Vankosoft\UsersBundle\Model;

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
}
