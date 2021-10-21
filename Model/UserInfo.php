<?php namespace VS\UsersBundle\Model;

use Doctrine\ORM\Mapping as ORM;

class UserInfo implements UserInfoInterface
{
    /**
     * @var mixed
     */
    protected $id;
    
    /**
     * Relation to the User entity
     *
     * @var mixed
     */
    protected $user;
    
    /**
     * @var string
     */
    protected $profilePictureFilename;
    
    /**
     * @var string
     */
    protected $country;
    
    /**
     * @var \DateTime|null
     */
    protected $birthday;
    
    /**
     * @var string
     */
    protected $mobile;
    
    /**
     * @var string
     */
    protected $website;
    
    /**
     * @var string
     */
    protected $occupation;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser( User $user ) : self
    {
        $this->user = $user;
        
        return $this;
    }
    
    public function getProfilePictureFilename()
    {
        return $this->profilePictureFilename;
    }
    
    public function setProfilePictureFilename( $profilePictureFilename )
    {
        $this->profilePictureFilename = $profilePictureFilename;
        
        return $this;
    }
    
    public function getCountry()
    {
        return $this->country;
    }
    
    public function setCountry( $country ) : self
    {
        $this->country = $country;
        
        return $this;
    }
    
    public function getBirthday()
    {
        return $this->birthday;
    }
    
    public function setBirthday( \DateTime $birthday ) : self
    {
        $this->birthday = $birthday;
        
        return $this;
    }
    
    public function getMobile()
    {
        return $this->mobile;
    }
    
    public function setMobile( $mobile ) : self
    {
        $this->mobile = $mobile;
        
        return $this;
    }
    
    public function getWebsite()
    {
        return $this->website;
    }
    
    public function setWebsite( $website ) : self
    {
        $this->website = $website;
        
        return $this;
    }
    
    public function getOccupation() {
        return $this->occupation;
    }
    
    public function setOccupation( $occupation ) : self
    {
        $this->occupation = $occupation;
        
        return $this;
    }
}
