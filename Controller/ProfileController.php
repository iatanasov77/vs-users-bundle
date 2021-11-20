<?php namespace VS\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sylius\Component\Resource\Factory\FactoryInterface;

use VS\CmsBundle\Component\Uploader\FileUploaderInterface;
use VS\UsersBundle\Form\ProfileFormType;
use VS\UsersBundle\Form\ChangePasswordFormType;
use VS\UsersBundle\Model\UserInfoInterface;

class ProfileController extends AbstractController
{
    /** @var FactoryInterface */
    private $avatarImageFactory;
    
    private FileUploaderInterface $imageUploader;
    
    public function __construct(
        FactoryInterface $avatarImageFactory,
        FileUploaderInterface $imageUploader
    ) {
        $this->avatarImageFactory   = $avatarImageFactory;
        $this->imageUploader        = $imageUploader;
    }
    
    public function profilePictureAction( Request $request ): Response
    {
        $profilePicture = false;
        
        $userInfo   = $this->getUser()->getInfo();
        if ( $userInfo && $userInfo->getProfilePictureFilename() ) {
            $profilePictureFileName = 'uploads/profile_pictures/' . $userInfo->getProfilePictureFilename();
            if ( file_exists( $profilePictureFileName ) ) {
                $profilePicture = $profilePictureFileName;
            }
        }
        
        if ( ! $profilePicture ) {
            $profilePicture = '/build/images/avatar-1.jpg';
        }
        
        return new Response( $profilePicture, Response::HTTP_OK );
    }
    
    public function indexAction( Request $request ) : Response
    {
        $em         = $this->getDoctrine()->getManager();
        $oUser      = $this->getUser();
        $form       = $this->createForm( ProfileFormType::class, $oUser, [
            'data'      => $oUser,
            'action'    => $this->generateUrl( 'vs_users_profile_show' ),
            'method'    => 'POST',
        ]);
        
        $otherForms = $this->forms( $request, $oUser );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $oUser          = $form->getData();
            
            if ( ! $oUser->getPreferedLocale() ) {
                $oUser->setPreferedLocale( $request->getLocale() );
            }
            
            $oUserInfo          = $oUser->getInfo();
            $profilePictureFile = $form->get( 'profilePicture' )->getData();
            if ( $profilePictureFile ) {
                $this->createAvatar( $oUserInfo, $profilePictureFile );
            }
            
            $oUserInfo->setFirstName( $form->get( 'firstName' )->getData() );
            $oUserInfo->setLastName( $form->get( 'lastName' )->getData() );
            
            $oUserInfo->setUser( $oUser );
            $em->persist( $oUserInfo );
            $em->persist( $oUser );
            $em->flush();
            
            return $this->redirectToRoute( 'vs_users_profile_show' );
        }
        
        return $this->render( '@VSUsers/Profile/show.html.twig', [
            'errors'        => $form->getErrors( true, false ),
            'form'          => $form->createView(),
            'user'          => $oUser,
            'otherForms'    => $otherForms,
        ]);
    }
    
    public function changePasswordAction( Request $request ) : Response
    {
        $em         = $this->getDoctrine()->getManager();
        $oUser      = $this->getUser();
        $forms      = $this->forms( $request, $oUser );
        $f          = $forms['changePasswordForm'];
        
        $f->handleRequest( $request );
        if ( $f->isSubmitted() && $f->isValid() ) {
            $userManager    = $this->container->get( 'vs_users.manager.user' );
            $data           = $request->request->all();

            $oldPassword        = $data['change_password_form']['oldPassword'];
            $newPassword        = $data['change_password_form']['password']['first'];
            $newPasswordConfirm = $data['change_password_form']['password']['second'];
            
            if ( ! $userManager->isPasswordValid( $oUser, $oldPassword ) ) {
                throw new \Exception( 'Invalid Old Password !!!' );
            }
            
            if ( $newPassword !== $newPasswordConfirm ) {
                throw new \Exception( 'Passwords Not Equals !!!' );
            }
            
            $userManager->encodePassword( $oUser, $newPassword );
            $em->persist( $oUser );
            $em->flush();
        }
        
        return $this->redirectToRoute( 'vs_users_profile_show' );
    }
    
    protected function forms( Request $request, $oUser ) : array
    {
        $changePasswordForm = $this->createForm( ChangePasswordFormType::class, $oUser, [
            'data'      => $oUser,
            'action'    => $this->generateUrl( 'vs_users_profile_change_password' ),
            'method'    => 'POST',
        ]);
        
        return [
            'changePasswordForm'    => $changePasswordForm,
        ];
    }
    
    private function createAvatar( UserInfoInterface &$userInfo, File $file ): void
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
