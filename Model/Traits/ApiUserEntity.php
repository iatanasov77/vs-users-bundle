<?php namespace Vankosoft\UsersBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

trait ApiUserEntity
{
    /** @var string */
    #[ORM\Column(name: "api_verify_siganature", type: "string", length: 255, nullable: true)]
    protected $apiVerifySiganature;
    
    /** @var \DateTime | null */
    #[ORM\Column(name: "api_verify_expires_at", type: Types::DATETIME_MUTABLE, nullable: true)]
    protected $apiVerifyExpiresAt;
    
    public function getApiVerifySiganature(): ?string
    {
        return $this->apiVerifySiganature;
    }
    
    public function setApiVerifySiganature( ?string $apiVerifySiganature ): self
    {
        $this->apiVerifySiganature = $apiVerifySiganature;
        
        return $this;
    }
    
    public function getApiVerifyExpiresAt(): ?\DateTime
    {
        return $this->apiVerifySiganature;
    }
    
    public function setApiVerifyExpiresAt( ?\DateTime $apiVerifyExpiresAt ): self
    {
        $this->apiVerifyExpiresAt = $apiVerifyExpiresAt;
        
        return $this;
    }
}
