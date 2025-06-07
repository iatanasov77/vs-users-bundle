<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Vankosoft\ApplicationBundle\Controller\Traits\CategoryTreeDataTrait;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\ApplicationBundle\Repository\TaxonRepository;

class UsersRolesExtController extends AbstractController
{
    use CategoryTreeDataTrait;
    
    /** @var TranslatorInterface */
    protected $translator;
    
    /** @var RepositoryInterface */
    protected $usersRepository;
    
    /** @var RepositoryInterface */
    protected $usersRolesRepository;
    
    /** @var RepositoryInterface */
    protected $taxonomyRepository;
    
    /** @var TaxonRepository */
    protected $taxonRepository;
    
    public function __construct(
        TranslatorInterface $translator,
        RepositoryInterface $usersRepository,
        RepositoryInterface $usersRolesRepository,
        RepositoryInterface $taxonomyRepository,
        TaxonRepository $taxonRepository
    ) {
        $this->translator           = $translator;
        $this->usersRepository      = $usersRepository;
        $this->usersRolesRepository = $usersRolesRepository;
        $this->taxonomyRepository   = $taxonomyRepository;
        $this->taxonRepository      = $taxonRepository;
    }
    
    public function rolesEasyuiComboTreeWithSelectedSource( $roleId, Request $request ): JsonResponse
    {
        $selectedParent = $roleId ? $this->usersRolesRepository->find( $roleId )->getParent() : null;
        $data[0]           = [
            'id'        => 0,
            'text'      => $this->translator->trans( 'vs_users.form.user_role.parent_role_no_parent', [], 'VSUsersBundle' ),
            'children'  => []
        ];
        
        $topRoles   = $this->usersRolesRepository->findBy( ['parent' => null] );
        $rolesTree  = [];
        
        $this->getItemsTree( new ArrayCollection( $topRoles ), $rolesTree );
        $this->buildEasyuiCombotreeDataFromCollection( $rolesTree, $data[0]['children'], [$selectedParent] );
        
        return new JsonResponse( $data );
    }
}
