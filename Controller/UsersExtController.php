<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\CmsBundle\Component\Uploader\FileUploaderInterface;
use Vankosoft\UsersBundle\Component\UserRole;
use Vankosoft\UsersBundle\Form\UserInfoForm;
use Vankosoft\UsersBundle\Model\Interfaces\UserInfoInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserRoleInterface;
use Vankosoft\UsersBundle\Model\UserRole as UserRoleModel;

class UsersExtController extends AbstractController
{
    use UserRolesAwareTrait;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
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
    
    /** @var bool */
    protected $allowCreateUserSiblings;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RepositoryInterface $usersRepository,
        FactoryInterface $userInfoFactory,
        FactoryInterface $avatarImageFactory,
        FileUploaderInterface $imageUploader,
        RepositoryInterface $usersRolesRepository,
        bool $allowCreateUserSiblings
    ) {
        $this->doctrine                 = $doctrine;
        $this->usersRepository          = $usersRepository;
        $this->userInfoFactory          = $userInfoFactory;
        $this->avatarImageFactory       = $avatarImageFactory;
        $this->imageUploader            = $imageUploader;
        $this->usersRolesRepository     = $usersRolesRepository;
        $this->allowCreateUserSiblings  = $allowCreateUserSiblings;
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
    
    public function rolesEasyuiComboTreeWithSelectedSource( $currentUserId, $editUserId, Request $request ): JsonResponse
    {
        $currentUser    = $currentUserId ? $this->usersRepository->find( $currentUserId ) : null;
        $editUser       = $editUserId ? $this->usersRepository->find( $editUserId ) : null;
        $selectedRoles  = $editUser  ? $editUser ->getRoles() : [];
        $data           = [];
        
        $userTopRole    = $currentUser->topRole();
        $topRoles       = new ArrayCollection( $this->usersRolesRepository->findBy( ['parent' => null] ) );
        
        /**
         * $topRoles->first() MUST TO BE 'ROLE_SUPER_ADMIN' AND 'ROLE_APPLICATION_ADMIN' TO BE HIS CHILD
         * 
         * SUPER WORKAROUND
         */
        if ( $userTopRole->getRole() == 'ROLE_APPLICATION_ADMIN' ) {
            if ( $this->allowCreateUserSiblings ) {
                $topRoles   =   $topRoles->first()->getChildren();
            } else {
                $topRoles   =   $topRoles->first()->getChildren()->first()->getChildren();
            }
        }
        
        $rolesTree      = [];
        $this->getRolesTree( $topRoles, $rolesTree );
        $this->buildEasyuiCombotreeDataFromCollection( $rolesTree, $data, $selectedRoles, [UserRoleModel::ANONYMOUS] );
        
        return new JsonResponse( $data );
    }
    
    protected function createAvatar( UserInfoInterface &$userInfo, File $file ): void
    {
        $avatarImage    = $userInfo->getAvatar() ?: $this->avatarImageFactory->createNew();
        $avatarImage->setOriginalName( $file->getClientOriginalName() );
        
        $uploadedFile   = new UploadedFile( $file->getRealPath(), $file->getBasename() );
        $avatarImage->setFile( $uploadedFile );
        $this->imageUploader->upload( $avatarImage );
        $avatarImage->setFile( null ); // reset File Because: Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed
        
        if ( ! $userInfo->getAvatar() ) {
            $userInfo->setAvatar( $avatarImage );
        }
    }
}
