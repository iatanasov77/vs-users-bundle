<?php namespace Vankosoft\UsersBundle\Model;

use Doctrine\Common\Comparable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Traits\TaxonDescendentTrait;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserRoleInterface;

class UserRole implements UserRoleInterface, Comparable
{
    use TaxonDescendentTrait;
    
    //const DEFAULT = 'ROLE_USER';
    const ANONYMOUS     = 'ROLE_ANONYMOUS_USER';
    const SUPER_ADMIN   = 'ROLE_SUPER_ADMIN';
    const ADMIN         = 'ROLE_ADMIN';
    const USER_PREMIUM  = 'ROLE_USER_PREMIUM';
    
    /** @var integer */
    protected $id;
    
    /** @var string */
    protected $role;
    
    /** @var UserRoleInterface */
    protected $parent;
    
    /** @var Collection|PageCategory[] */
    protected $children;
    
    /** @var Collection|User[] */
    protected $users;
    
    /**
     * The Users Thant Allowed to use only this role
     * 
     * @var Collection|User[]
     */
    protected $usersAllowed;
    
    public function __construct()
    {
        $this->children     = new ArrayCollection();
        $this->users        = new ArrayCollection();
        $this->usersAllowed = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getRole()
    {
        return $this->role;
    }
    
    public function setRole( $role ): self
    {
        $this->role = $role;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setParent(?UserRoleInterface $parent): self
    {
        $this->parent = $parent;
        
        return $this;
    }
    
    public function getChildren() : Collection
    {
        return $this->children;
    }
    
    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }
    
    public function addUser( UserInterface $user ): self
    {
        if ( ! $this->users->contains( $user ) ) {
            $this->users[] = $user;
            $user->addRole( $this );
        }
        
        return $this;
    }
    
    public function removeUser( UserInterface $user ): self
    {
        if ( ! $this->users->contains( $user ) ) {
            $this->users->removeElement( $user );
            $user->removeRole( $this );
        }
        
        return $this;
    }
    
    /**
     * @return Collection|User[]
     */
    public function getUsersAllowed(): Collection
    {
        return $this->usersAllowed;
    }
    
    public function addUsersAllowed( UserInterface $user ): self
    {
        if ( ! $this->usersAllowed->contains( $user ) ) {
            $this->usersAllowed[] = $user;
            $user->addAllowedRole( $this );
        }
        
        return $this;
    }
    
    public function removeUsersAllowed( UserInterface $user ): self
    {
        if ( ! $this->usersAllowed->contains( $user ) ) {
            $this->usersAllowed->removeElement( $user );
            $user->removeAllowedRole( $this );
        }
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Doctrine\Common\Comparable::compareTo($other)
     */
    public function compareTo($other): int
    {
        if ( ! ( $other instanceof UserRoleInterface ) ) {
            throw new \Exception( 'Vankosoft UserRole can to be Compared with other Vankosoft UserRole Objects !!!' );
        }
        
        if ( $this->taxon->getCode() === $other->getTaxon()->getCode() ) {
            return 0;
        } elseif ( $this->taxon->getParent() && $this->taxon->getParent()->getCode() === $other->getTaxon()->getCode() ) {
            return -1;
        } else {
            return 1;
        }
    }
    
    public function __toString()
    {
        return $this->role;
    }
}