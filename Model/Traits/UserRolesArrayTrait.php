<?php namespace Vankosoft\UsersBundle\Model\Traits;

trait UserRolesArrayTrait
{
    /**
     * @var array
     */
    protected $rolesArray = [];
    
    /**
     * @return array
     */
    public function getRolesFromArray(): array
    {
        $roles  = $this->rolesArray ?: [];
        
        // we need to make sure to have at least one role
        //$roles[] = "ROLE_DEFAULT";
        
        return array_unique( $roles );
    }
    
    public function setRolesArray( array $roles ): self
    {
        $this->rolesArray    = $roles;
        
        return $this;
    }
}
