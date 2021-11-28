<?php namespace VS\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use VS\ApplicationBundle\Component\Status;
use VS\ApplicationBundle\Repository\TaxonRepository;

class UsersRolesExtController extends AbstractController
{
    /** @var RepositoryInterface */
    protected $usersRepository;
    
    /** @var RepositoryInterface */
    protected $usersRolesRepository;
    
    /** @var RepositoryInterface */
    protected $taxonomyRepository;
    
    /** @var TaxonRepository */
    protected $taxonRepository;
    
    public function __construct(
        RepositoryInterface $usersRepository,
        RepositoryInterface $usersRolesRepository,
        RepositoryInterface $taxonomyRepository,
        TaxonRepository $taxonRepository
    ) {
        $this->usersRepository      = $usersRepository;
        $this->usersRolesRepository = $usersRolesRepository;
        $this->taxonomyRepository   = $taxonomyRepository;
        $this->taxonRepository      = $taxonRepository;
    }
    
    public function rolesEasyuiComboTreeWithSelectedSource( $userId, Request $request ): JsonResponse
    {
            $selectedRoles  = $userId ? $this->usersRepository->find( $userId )->getRoles() : [];
            $data           = [];
            
            $topRoles   = $this->usersRolesRepository->findBy( ['parent' => null] );
            $rolesTree  = [];
            $this->getRolesTree( new ArrayCollection( $topRoles ), $rolesTree );
            
            $this->buildEasyuiCombotreeData( $rolesTree, $data, $selectedRoles );
            
            return new JsonResponse( $data );
    }
    
    protected function buildEasyuiCombotreeData( $tree, &$data, array $selectedValues )
    {
        $key    = 0;
        
        if ( is_array( $tree ) ) {
            foreach( $tree as $nodeKey => $node ) {
                $data[$key]   = [
                    'id'        => $node['id'],
                    'text'      => $node['role'],
                    'children'  => []
                ];
                if ( in_array( $nodeKey, $selectedValues ) ) {
                    $data[$key]['checked'] = true;
                }
                
                if ( ! empty( $node['children'] ) ) {
                    $this->buildEasyuiCombotreeData( $node['children'], $data[$key]['children'], $selectedValues );
                }
                
                $key++;
            }
        }
    }
    
    private function getRolesTree( Collection $roles, &$rolesTree )
    {
        foreach ( $roles as $role ) {
            $rolesTree[$role->getRole()] = [
                'id'        => $role->getId(),
                'role'      => $role->getRole(),
                'children'  => [],
            ];
            
            if ( ! $role->getChildren()->isEmpty() ) {
                $this->getRolesTree( $role->getChildren(), $rolesTree[$role->getRole()]['children'] );
            }
        }
    }
}
