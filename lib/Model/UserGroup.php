<?php namespace VS\UsersBundle\Model;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

class UserGroup extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    protected $roles;
    
    public function __construct( $roles = [] )
    {
        $this->roles = $roles;
        parent::__construct();
    }
}
