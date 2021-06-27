<?php  namespace VS\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use VS\UsersBundle\Component\UserRole;

class UsersExtController
{
    /* RepositoryInterface */
    protected $usersRepository;
    
    public function __construct( RepositoryInterface $usersRepository )
    {
        $this->usersRepository  = $usersRepository;
    }
    
    public function rolesEasyuiComboTreeWithSelectedSource(
        $userId,
        Request $request
    ): Response {
        $selectedRoles  = $userId ? $this->usersRepository->find( $userId )->getRoles() : [];
        $data           = [];
        $this->buildEasyuiCombotreeData( UserRole::choicesTree(), $data, $selectedRoles );
        
        return new JsonResponse( $data );
    }
    
    protected function buildEasyuiCombotreeData( $tree, &$data, array $selectedValues )
    {
        $key    = 0;
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
