<?php namespace Vankosoft\UsersBundle\Model\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface UserInterface extends BaseUserInterface, PasswordAuthenticatedUserInterface, ResourceInterface
{
    public function hasRole( string $role ): bool;
    
    public function getRolesFromArray(): array;
    
    public function getRolesFromCollection(): array;
    
    /** @return Collection|UserActivity[] */
    public function getActivities(): Collection;
    
    /** @return Collection|UserNotification[] */
    public function getNotifications(): Collection;
}
