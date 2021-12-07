<?php namespace VS\UsersBundle\Model\Traits;

use Doctrine\Common\Collections\Collection;
use VS\UsersBundle\Model\UserInterface;
use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use VS\ApplicationBundle\Model\Application;

trait UserApplicationsTrait
{
    /**
     * The Applications for wich the user tobe granted if she have not ROLE_SUPER_ADMIN
     * 
     * @var Collection|Application[]
     */
    protected $applications;
    
    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }
    
    public function addApplication( ApplicationInterface $application ): UserInterface
    {
        if ( ! $this->applications->contains( $application ) ) {
            $this->applications[] = $application;
            $application->addRole( $this );
        }
        
        return $this;
    }
    
    public function removeApplication( ApplicationInterface $application ): UserInterface
    {
        if ( ! $this->applications->contains( $application ) ) {
            $this->applications->removeElement( $application );
            $application->removeRole( $this );
        }
        
        return $this;
    }
}
