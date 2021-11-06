<?php namespace VS\UsersBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use VS\ApplicationBundle\Model\Interfaces\TaxonInterface;
use VS\ApplicationBundle\Model\Taxon;

class UserRole implements UserRoleInterface
{
    //const DEFAULT = 'ROLE_USER';
    const SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ADMIN = 'ROLE_ADMIN';
    const USER_PREMIUM = 'ROLE_USER_PREMIUM';
    
    /** @var mixed */
    protected $id;
    
    /** @var string */
    protected $role;
    
    /** @var UserRoleInterface */
    protected $parent;
    
    /** @var Collection|PageCategory[] */
    protected $children;
    
    /** @var Collection|User[] */
    protected $users;
    
    /** @var TaxonInterface */
    protected $taxon;
    
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->users    = new ArrayCollection();
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
    
    public function setRole( $role ) : UserRoleInterface
    {
        $this->role = $role;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getParent(): ?UserRoleInterface
    {
        return $this->parent;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setParent(?UserRoleInterface $parent) : UserRoleInterface
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
    
    public function addUser( UserInterface $user ) : UserRoleInterface
    {
        if ( ! $this->users->contains( $user ) ) {
            $this->users[] = $user;
            $user->addRole( $this );
        }
        
        return $this;
    }
    
    public function removeUser( UserInterface $user ) : UserRoleInterface
    {
        if ( ! $this->users->contains( $user ) ) {
            $this->users->removeElement( $user );
            $user->removeRole( $this );
        }
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTaxon(): ?TaxonInterface
    {
        return $this->taxon;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setTaxon(?TaxonInterface $taxon): void
    {
        $this->taxon = $taxon;
    }
    
    public function getName()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
    
    public function setName( string $name ) : self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setName( $name );
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->role;
    }
}
