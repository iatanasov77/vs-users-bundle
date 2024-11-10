<?php namespace Vankosoft\UsersBundle\Model\Interfaces;

interface ApiUserInterface
{
    public function getApiVerifySiganature(): ?string;
    public function setApiVerifySiganature( ?string $apiVerifySiganature ): self;
    public function getApiVerifyExpiresAt(): ?\DateTime;
    public function setApiVerifyExpiresAt( ?\DateTime $apiVerifyExpiresAt ): self;
}
