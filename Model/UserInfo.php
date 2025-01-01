<?php namespace Vankosoft\UsersBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Vankosoft\CmsBundle\Model\Interfaces\FileInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserInfoInterface;

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
     * @var FileInterface|null
     */
    protected $avatar;
    
    /**
     * @var string
     */
    protected $title        = 'miss';
    
    /**
     * @var string
     */
    protected $firstName    = 'NOT_EDITED_YET';
    
    /**
     * @var string
     */
    protected $lastName     = 'NOT_EDITED_YET';
    
    /**
     * @var string
     */
    protected $designation  = 'Lead Designer / Developer';
    
    /**
     * @var \DateTime|null
     */
    protected $birthday;
    
    /**
     * @var string
     */
    protected $phone;
    
    /**
     * @var string
     */
    protected $mobile;
    
    /**
     * @var string
     */
    protected $country;
    
    /**
     * @var string
     */
    protected $city;
    
    /**
     * @var string
     */
    protected $state;
    
    /**
     * @var string
     */
    protected $zip;
    
    /**
     * @var string
     */
    protected $address;
    
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
    
    public function getAvatar(): ?FileInterface
    {
        return $this->avatar;
    }
    
    public function setAvatar( ?FileInterface $avatar ): self
    {
        $avatar->setOwner( $this );
        $this->avatar   = $avatar;
        
        return $this;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle( $title ) : self
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    public function setFirstName( $firstName ) : self
    {
        $this->firstName = $firstName;
        
        return $this;
    }
    
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function setLastName( $lastName ) : self
    {
        $this->lastName = $lastName;
        
        return $this;
    }
    
    public function getDesignation()
    {
        return $this->designation;
    }
    
    public function setDesignation( $designation ) : self
    {
        $this->designation = $designation;
        
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
    
    public function getPhone()
    {
        return $this->phone;
    }
    
    public function setPhone( $phone ) : self
    {
        $this->phone = $phone;
        
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
    
    public function getCountry()
    {
        return $this->country;
    }
    
    public function setCountry( $country ) : self
    {
        $this->country = $country;
        
        return $this;
    }
    
    public function getCity()
    {
        return $this->city;
    }
    
    public function setCity( $city ) : self
    {
        $this->city = $city;
        
        return $this;
    }
    
    public function getState()
    {
        return $this->state;
    }
    
    public function setState( $state ) : self
    {
        $this->state = $state;
        
        return $this;
    }
    
    public function getZip()
    {
        return $this->zip;
    }
    
    public function setZip( $zip ) : self
    {
        $this->zip = $zip;
        
        return $this;
    }
    
    public function getAddress()
    {
        return $this->address;
    }
    
    public function setAddress( $address ) : self
    {
        $this->address = $address;
        
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
    
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
