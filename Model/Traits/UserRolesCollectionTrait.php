<?php namespace Vankosoft\UsersBundle\Model\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Vankosoft\UsersBundle\Model\UserRole;
use Vankosoft\UsersBundle\Model\Interfaces\UserRoleInterface;

trait UserRolesCollectionTrait
{
    /**
     * @var Collection|UserRole[]
     *
     * https://symfony.com/doc/current/security.html#hierarchical-roles
     */
    protected $rolesCollection;
    
    /**
     * @return array
     */
    public function getRolesFromCollection(): array
    {        
        $roles  = [];
        foreach ( $this->rolesCollection as $role ) {
            $roles[]    = $role->getRole();
        }
        
        return \array_unique( $roles );
    }
    
    /**
     * @return Collection|UserRole[]
     */
    public function getRolesCollection(): Collection
    {
        return $this->rolesCollection;
    }
    
    public function setRolesCollection( Collection $rolesCollection ): self
    {
        $this->rolesCollection  = $rolesCollection;
        
        return $this;
    }
    
    public function addRole( UserRoleInterface $role ): self
    {
        if ( ! $this->rolesCollection->contains( $role ) ) {
            $this->rolesCollection[]    = $role;
        }
        
        return $this;
    }
    
    public function removeRole( UserRoleInterface $role ): self
    {
        if ( $this->rolesCollection->contains( $role ) ) {
            $this->rolesCollection->removeElement( $role );
        }
        
        return $this;
    }
    
    public function getRolesAncestors(): Collection
    {
        $ancestors = [];
        
        foreach ( $this->getRolesCollection() as $r ) {
            for ( $ancestor = $r->getParent(); null !== $ancestor; $ancestor = $ancestor->getParent() ) {
                array_push( $ancestors, $ancestor );
            }
        }
        
        return new ArrayCollection( $ancestors );
    }
}
