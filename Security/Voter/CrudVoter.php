<?php namespace Vankosoft\UsersBundle\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class CrudVoter extends Voter
{
    // these strings are just invented: you can use anything
    const LIST      = 'list';
    const VIEW      = 'view';
    const CREATE    = 'create';
    const EDIT      = 'edit';
    const REMOVE    = 'remove';
}
