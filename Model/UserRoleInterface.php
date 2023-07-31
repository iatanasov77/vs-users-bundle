<?php namespace Vankosoft\UsersBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\VankosoftCategoryInterface;
use Doctrine\Common\Collections\Collection;

interface UserRoleInterface extends VankosoftCategoryInterface
{
    public function getRole();
    public function getUsers(): Collection;
}
