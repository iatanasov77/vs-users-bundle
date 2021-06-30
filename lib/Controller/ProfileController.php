<?php namespace VS\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use VS\UsersBundle\Form\ProfileFormType;
use VS\UsersBundle\Form\ChangePasswordFormType;

class ProfileController extends AbstractController
{
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
            
            $profilePictureFile = $form->get( 'profilePicture' )->getData();
            if ( $profilePictureFile ) {
                $oUserInfo   = $oUser->getInfo();
                
                $oUserInfo->setProfilePictureFilename( $this->handleProfilePicture( $profilePictureFile ) );
                $oUserInfo->setUser( $oUser );
                $em->persist( $oUserInfo );
            }
            
            $em->persist( $oUser );
            $em->flush();
            
            return $this->redirectToRoute( $this->container->getParameter( 'vs_users.default_redirect' ) );
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
    
    protected function handleProfilePicture( $profilePictureFile ) : string
    {
        $originalFilename   = pathinfo( $profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME );
        // this is needed to safely include the file name as part of the URL
        //$safeFilename       = $slugger->slug( $originalFilename );    // Slugger is included in Symfony 5.0
        $safeFilename       = $originalFilename;
        $newFilename        = $safeFilename . '-' . uniqid() . '.' . $profilePictureFile->guessExtension();
        
        // Move the file to the directory where brochures are stored
        try {
            $profilePictureFile->move(
                $this->getParameter( 'vs_user.profile_pictures_dir' ),
                $newFilename
            );
        } catch ( FileException $e ) {
            // ... handle exception if something happens during file upload
        }
        
        return $newFilename;
    }
}
