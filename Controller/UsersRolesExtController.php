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
            $this->getRolesTree( new Collection( $topRoles ) );
            
            $this->buildEasyuiCombotreeData( $rolesTree, $data, $selectedRoles );
            
            return new JsonResponse( $data );
    }
    
    protected function buildEasyuiCombotreeData( $tree, &$data, array $selectedValues )
    {
        $key    = 0;
        
        if ( is_array( $tree ) ) {
            foreach( $tree as $nodeKey => $nodeChildren ) {
                $data[$key]   = [
                    'id'        => $nodeKey,
                    'text'      => $nodeKey,
                    'children'  => []
                ];
                if ( in_array( $nodeKey, $selectedValues ) ) {
                    $data[$key]['checked'] = true;
                }
                
                if ( ! empty( $nodeChildren ) ) {
                    $this->buildEasyuiCombotreeData( $nodeChildren, $data[$key]['children'], $selectedValues );
                }
                
                $key++;
            }
        }
    }
    
    private function getRolesTree( Collection $roles, &$rolesTree )
    {
        foreach ( $roles as $role ) {
            $rolesTree[$role->getRole()] = [];
            
            if ( ! $role->getChildren()->isEmpty() ) {
                $this->getRolesTree( $role->getChildren(), $rolesTree[$role->getRole()] );
            }
        }
    }
//     private function getRolesTree( Collection $roles )
//     {
//         $taxonomyCode   = $this->getParameter( 'vs_application.user_roles.taxonomy_code' );
//         $taxonomy       = $this->taxonomyRepository->findByCode( $taxonomyCode );
        
//         $taxonTree      = $this->taxonRepository->childrenHierarchy( $taxonomy->getRootTaxon() );
//         $rolesTree      = [];
//         foreach ( $this->usersRolesRepository->findAll() as $role ) {
//             if ( ! $role->getChildren()->isEmpty() ) {
//                 $this->getRolesTree( $role->getChildren() );
//             }
//         }
        
//         return $rolesTree;
//     }
}
