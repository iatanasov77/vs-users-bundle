<?php namespace VS\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

use VS\CmsBundle\Component\Uploader\FileUploaderInterface;
use VS\UsersBundle\Component\UserRole;
use VS\UsersBundle\Form\UserInfoForm;
use VS\UsersBundle\Model\UserInfoInterface;

class UsersExtController extends AbstractController
{
    /** @var RepositoryInterface */
    protected $usersRepository;
    
    /** @var FactoryInterface */
    protected $userInfoFactory;
    
    /** @var FactoryInterface */
    protected $avatarImageFactory;
    
    /** @var FileUploaderInterface */
    protected $imageUploader;
    
    public function __construct(
        RepositoryInterface $usersRepository,
        FactoryInterface $userInfoFactory,
        FactoryInterface $avatarImageFactory,
        FileUploaderInterface $imageUploader
    ) {
        $this->usersRepository      = $usersRepository;
        $this->userInfoFactory      = $userInfoFactory;
        $this->avatarImageFactory   = $avatarImageFactory;
        $this->imageUploader        = $imageUploader;
    }
    
    public function displayUserInfo( $userId, Request $request ): Response
    {
        $user       = $this->usersRepository->find( $userId );
        $userInfo   = $user->info() ?: $this->userInfoFactory->createNew();
        
        return $this->render( '@VSUsers/UsersCrud/Partial/form_user_info.html.twig', [
            'form'      => $this->createForm( UserInfoForm::class, $userInfo )->createView(),
            'userInfo'  => $userInfo,
            'user'      => $user,
        ]);
    }
    
    public function handleUserInfo( $userId, Request $request ): Response
    {
        $userInfo   = $this->userInfoFactory->createNew();
        $form       = $this->createForm( UserInfoForm::class, $userInfo );
        $em         = $this->getDoctrine()->getManager();
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $userInfo   = $form->getData();
            $user       = $this->usersRepository->find( $userId );

            $profilePictureFile = $form->get( 'profilePicture' )->getData();
            if ( $profilePictureFile ) {
                $this->createAvatar( $userInfo, $profilePictureFile );
            }
            
            $userInfo->setUser( $user );
            $em->persist( $userInfo );
            $em->flush();
            
            return $this->redirectToRoute( 'vs_users_profile_show' );
        }
    }
    
    public function rolesEasyuiComboTreeWithSelectedSource( $userId, Request $request ): JsonResponse
    {
            $selectedRoles  = $userId ? $this->usersRepository->find( $userId )->getRoles() : [];
            $data           = [];
            $this->buildEasyuiCombotreeData( UserRole::choicesTree(), $data, $selectedRoles );
            
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
}
