<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\CmsBundle\Component\Uploader\FileUploaderInterface;
use Vankosoft\UsersBundle\Component\UserRole;
use Vankosoft\UsersBundle\Form\UserInfoForm;
use Vankosoft\UsersBundle\Model\UserInfoInterface;

class UsersExtController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var RepositoryInterface */
    protected $usersRepository;
    
    /** @var FactoryInterface */
    protected $userInfoFactory;
    
    /** @var FactoryInterface */
    protected $avatarImageFactory;
    
    /** @var FileUploaderInterface */
    protected $imageUploader;
    
    /** @var RepositoryInterface */
    protected $usersRolesRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RepositoryInterface $usersRepository,
        FactoryInterface $userInfoFactory,
        FactoryInterface $avatarImageFactory,
        FileUploaderInterface $imageUploader,
        RepositoryInterface $usersRolesRepository
    ) {
        $this->doctrine             = $doctrine;
        $this->usersRepository      = $usersRepository;
        $this->userInfoFactory      = $userInfoFactory;
        $this->avatarImageFactory   = $avatarImageFactory;
        $this->imageUploader        = $imageUploader;
        $this->usersRolesRepository = $usersRolesRepository;
    }
    
    public function displayUserInfo( $userId, Request $request ): Response
    {
        $user       = $this->usersRepository->find( $userId );
        $userInfo   = $user->getInfo() ?: $this->userInfoFactory->createNew();
        
        return $this->render( '@VSUsers/UsersCrud/Partial/form_user_info.html.twig', [
            'form'      => $this->createForm( UserInfoForm::class, $userInfo )->createView(),
            'userInfo'  => $userInfo,
            'user'      => $user,
        ]);
    }
    
    public function handleUserInfo( $userId, Request $request ): JsonResponse
    {
        $user       = $this->usersRepository->find( $userId );
        $userInfo   = $user->getInfo() ?: $this->userInfoFactory->createNew();
        $form       = $this->createForm( UserInfoForm::class, $userInfo );
        $em         = $this->doctrine->getManager();
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $userInfo   = $form->getData();
            
            $profilePictureFile = $form->get( 'profilePicture' )->getData();
            if ( $profilePictureFile ) {
                $this->createAvatar( $userInfo, $profilePictureFile );
            }
            
            $user->setInfo( $userInfo );
            $em->persist( $userInfo );
            $em->persist( $user );
            $em->flush();
            
            return new JsonResponse([
                'status'   => Status::STATUS_OK
            ]);
        }
        
        return new JsonResponse([
            'status'   => Status::STATUS_ERROR
        ]);
    }
    
    public function rolesEasyuiComboTreeWithSelectedSource( $userId, Request $request ): JsonResponse
    {
            $selectedRoles  = $userId ? $this->usersRepository->find( $userId )->getRoles() : [];
            $data           = [];
            
            $topRoles       = $this->usersRolesRepository->findBy( ['parent' => null] );
            $rolesTree      = [];
            $this->getRolesTree( new ArrayCollection( $topRoles ), $rolesTree );
            $this->buildEasyuiCombotreeDataFromCollection( $rolesTree, $data, $selectedRoles );
            
            //$this->buildEasyuiCombotreeData( UserRole::choicesTree(), $data, $selectedRoles );
            
            return new JsonResponse( $data );
    }
    
    protected function buildEasyuiCombotreeDataFromCollection( $tree, &$data, array $selectedValues )
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
                    $this->buildEasyuiCombotreeDataFromCollection( $node['children'], $data[$key]['children'], $selectedValues );
                }
                
                $key++;
            }
        }
    }
    
    /**
     * OLD Way
     */
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
    
    protected function createAvatar( UserInfoInterface &$userInfo, File $file ): void
    {
        $avatarImage    = $userInfo->getAvatar() ?: $this->avatarImageFactory->createNew();
        $uploadedFile   = new UploadedFile( $file->getRealPath(), $file->getBasename() );
        
        $avatarImage->setFile( $uploadedFile );
        $this->imageUploader->upload( $avatarImage );
        $avatarImage->setFile( null ); // reset File Because: Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed
        
        if ( ! $userInfo->getAvatar() ) {
            $userInfo->setAvatar( $avatarImage );
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
