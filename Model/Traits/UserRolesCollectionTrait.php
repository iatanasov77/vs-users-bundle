<?php namespace VS\UsersBundle\Model\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use VS\UsersBundle\Model\UserRole;
use VS\UsersBundle\Model\UserRoleInterface;

trait UserRolesCollectionTrait
{
    /**
     * @var Collection|UserRole[]
     *
     * https://symfony.com/doc/current/security.html#hierarchical-roles
     */
    protected $rolesCollection;
 
    public function __construct()
    {
        parent::__construct();
        
        $this->rolesCollection  = new ArrayCollection();
    }
    
    /**
     * @return array
     */
    public function getRolesFromCollection(): array
    {
        // we need to make sure to have at least one role
        $roles  = ["ROLE_DEFAULT"];
        
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
}
