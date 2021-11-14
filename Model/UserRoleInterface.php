<?php namespace VS\UsersBundle\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface UserRoleInterface extends ResourceInterface
{
    public function getName();
    public function getRole();
    
    public function getParent(): ?UserRoleInterface;
    public function getChildren() : Collection;
    
    public function getUsers(): Collection;
}
