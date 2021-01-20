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
    protected $apiToken;
    
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
    
    public function getApiToken()
    {
        return $this->apiToken;
    }
    
    public function setApiToken( $apiToken ) : self
    {
        $this->apiToken = $apiToken;
        
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
