<?php namespace VS\UsersBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface UserInterface extends BaseUserInterface, ResourceInterface
{
    /** @return Collection|UserActivity[] */
    public function getActivities() : Collection;
    
    /** @return Collection|UserNotification[] */
    public function getNotifications() : Collection;
}
