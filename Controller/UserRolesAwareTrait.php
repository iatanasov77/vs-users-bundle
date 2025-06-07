<?php namespace Vankosoft\UsersBundle\Controller;

use Doctrine\Common\Collections\Collection;

trait UserRolesAwareTrait
{
    protected function buildEasyuiCombotreeDataFromCollection( $tree, &$data, array $selectedValues, array $ignoreRoles = [] )
    {
        $key    = 0;
        
        if ( \is_array( $tree ) ) {
            foreach( $tree as $nodeKey => $node ) {
                if ( \in_array( $node['role'], $ignoreRoles ) ) {
                    continue;
                }
                
                $data[$key]   = [
                    'id'        => $node['id'],
                    'text'      => $node['role'],
                    'children'  => []
                ];
                if ( \in_array( $nodeKey, $selectedValues ) ) {
                    $data[$key]['checked'] = true;
                }
                
                if ( ! empty( $node['children'] ) ) {
                    $this->buildEasyuiCombotreeDataFromCollection( $node['children'], $data[$key]['children'], $selectedValues, $ignoreRoles );
                }
                
                $key++;
            }
        }
    }
    
    protected function getRolesTree( Collection $roles, &$rolesTree, Collection $allowedRoles )
    {
        foreach ( $roles as $role ) {
            if ( ! $allowedRoles->isEmpty() && ! $allowedRoles->contains( $role ) ) {
                continue;
            }
            
            $rolesTree[$role->getRole()] = [
                'id'        => $role->getId(),
                'role'      => $role->getRole(),
                'children'  => [],
            ];
            
            if ( ! $role->getChildren()->isEmpty() ) {
                $this->getRolesTree( $role->getChildren(), $rolesTree[$role->getRole()]['children'], $allowedRoles );
            }
        }
    }
}