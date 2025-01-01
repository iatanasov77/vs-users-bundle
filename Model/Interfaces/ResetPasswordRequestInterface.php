<?php namespace Vankosoft\UsersBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface as BaseResetPasswordRequestInterface;

interface ResetPasswordRequestInterface extends BaseResetPasswordRequestInterface, ResourceInterface
{
    
}
