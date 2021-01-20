<?php namespace VS\UsersBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface UserInterface extends BaseUserInterface, ResourceInterface
{
    
}
