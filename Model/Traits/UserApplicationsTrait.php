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
    
    /**
     * Used before setApplications method to fix when removing an application
     * MANUAL: https://stackoverflow.com/questions/38955114/symfony-doctrine-remove-manytomany-association/38955917
     * 
     * @return self
     */
    public function clearApplications(): self
    {
        foreach ( $this->applications as $app ) {
            $this->removeApplication( $app );
        }
        
        return $this;
    }
    
    public function addApplication( ApplicationInterface $application ): UserInterface
    {
        if ( ! $this->applications->contains( $application ) ) {
            $this->applications[] = $application;
            $application->addUser( $this );
        }
        
        return $this;
    }
    
    public function removeApplication( ApplicationInterface $application ): UserInterface
    {
        if ( $this->applications->contains( $application ) ) {
            $this->applications->removeElement( $application );
            $application->removeUser( $this );
        }
        
        return $this;
    }
}
