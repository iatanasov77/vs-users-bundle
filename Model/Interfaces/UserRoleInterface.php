<?php namespace Vankosoft\UsersBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\VankosoftCategoryInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonDescendentInterface;
use Doctrine\Common\Collections\Collection;

interface UserRoleInterface extends VankosoftCategoryInterface, TaxonDescendentInterface
{
    public function getRole();
    public function getUsers(): Collection;
}
