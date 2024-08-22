<?php namespace Vankosoft\UsersBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface UserActivityInterface extends ResourceInterface
{
    /** @return integer */
    public function getId();
    
    /** @return string */
    public function getUser();
    
    /** @return \DateTimeInterface */
    public function getDate();
    
    /** @return string */
    public function getActivity();
}
