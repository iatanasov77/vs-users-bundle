<?php namespace VS\UsersBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class UserRolesRepository extends EntityRepository
{
    public function findByTaxonId( $taxonId )
    {
        $allRoles   = $this->findAll();
        foreach ( $allRoles as $role ) {
            if ( $role->getTaxon()->getId() == $taxonId ) {
                return $role;
            }
        }
        
        return null;
    }
    
    public function findByTaxonCode( $code )
    {
        $allRoles   = $this->findAll();
        foreach ( $allRoles as $role ) {
            if ( $role->getTaxon()->getCode() == $code ) {
                return $role;
            }
        }
        
        return null;
    }
}
