<?php namespace Vankosoft\UsersBundle\Model\Traits;

use Doctrine\Common\Collections\Collection;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\ApplicationBundle\Model\Application;
use Vankosoft\UsersBundle\Model\Interfaces\UserRoleInterface;

trait UserApplicationsTrait
{
    /**
     * The Applications for wich the user to be granted if she have not ROLE_SUPER_ADMIN
     * 
     * @var Collection|Application[]
     */
    protected $applications;
    
    /**
     * The User Roles for wich the user can create users and view users
     * if she have not ROLE_SUPER_ADMIN or this collection is not empty
     *
     * @var Collection|UserRoleInterface[]
     */
    protected $allowedRoles;
    
    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }
    
    /**
     * MANUAL: https://jeremymarc.github.io/2013/01/31/symfony-form-and-doctrine-inverse-side-association
     * 
     * @param Collection $applications
     * @return self
     */
    public function setApplications( Collection $applications ): self
    {
        $this->applications = $applications;
        foreach ( $applications as $app ) {
            $app->addUser( $this );
        }
        
        return $this;
    }
    
    public function addApplication( ApplicationInterface $application ): self
    {
        if ( ! $this->applications->contains( $application ) ) {
            $this->applications[] = $application;
            $application->addUser( $this );
        }
        
        return $this;
    }
    
    public function removeApplication( ApplicationInterface $application ): self
    {
        if ( $this->applications->contains( $application ) ) {
            $this->applications->removeElement( $application );
            $application->removeUser( $this );
        }
        
        return $this;
    }
    
    /**
     * @return Collection|UserRoleInterface[]
     */
    public function getAllowedRoles(): Collection
    {
        return $this->allowedRoles;
    }
    
    /**
     * @param Collection $allowedRoles
     * @return self
     */
    public function setAllowedRoles( Collection $allowedRoles ): self
    {
        $this->allowedRoles = $allowedRoles;
        
        return $this;
    }
    
    public function addAllowedRole( UserRoleInterface $allowedRole ): self
    {
        if ( ! $this->allowedRoles->contains( $allowedRole ) ) {
            $this->allowedRoles[] = $allowedRole;
        }
        
        return $this;
    }
    
    public function removeAllowedRole( UserRoleInterface $allowedRole ): self
    {
        if ( $this->allowedRoles->contains( $allowedRole ) ) {
            $this->allowedRoles->removeElement( $allowedRole );
        }
        
        return $this;
    }
}
