<?php namespace VS\UsersBundle\Model;

use Doctrine\Common\Collections\Collection;

interface UserRoleInterface
{
    public function getName();
    public function getRole();
    
    public function getParent(): ?UserRoleInterface;
    public function getChildren() : Collection;
    
    public function getUsers(): Collection;
}
