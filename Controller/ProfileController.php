<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\FactoryInterface;

use Vankosoft\CmsBundle\Component\Uploader\FileUploaderInterface;
use Vankosoft\UsersBundle\Form\ProfileFormType;
use Vankosoft\UsersBundle\Form\ChangePasswordFormType;
use Vankosoft\UsersBundle\Form\ProfilePictureForm;
use Vankosoft\UsersBundle\Model\UserInfoInterface;
use Vankosoft\UsersBundle\Security\UserManager;

use Vankosoft\AgentBundle\Component\VankosoftAgent;

class ProfileController extends AbstractController
{
    const EXTENSION_PAYMENT             = 'VSPaymentBundle';
    const EXTENSION_USERSUBSCRIPTIONS   = 'VSUsersSubscriptionsBundle';
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var string */
    protected $usersClass;
    
    /** @var UserManager */
    private $userManager;
    
    /** @var FactoryInterface */
    private $avatarImageFactory;
    
    /** @var FileUploaderInterface */
    private $imageUploader;
    
    /** @var VankosoftAgent */
    private $vankosoftAgent;
    
    public function __construct(
        ManagerRegistry $doctrine,
        string $usersClass,
        UserManager $userManager,
        FactoryInterface $avatarImageFactory,
        FileUploaderInterface $imageUploader,
        VankosoftAgent $vankosoftAgent
    ) {
        $this->doctrine             = $doctrine;
        $this->usersClass           = $usersClass;
        $this->userManager          = $userManager;
        $this->avatarImageFactory   = $avatarImageFactory;
        $this->imageUploader        = $imageUploader;
        $this->vankosoftAgent       = $vankosoftAgent;
    }
    
    /**
     * Show User Profile
     * 
     * @param Request $request
     * @return Response
     */
    public function indexAction( Request $request ): Response
    {
        return $this->render( '@VSUsers/Profile/show.html.twig', $this->templateParams( $this->getProfileEditForm() ) );
    }
    
    /**
     * Edit User Profile
     *
     * @param Request $request
     * @return Response
     */
    public function editAction( Request $request ): Response
    {
        return $this->render( '@VSUsers/Profile/edit.html.twig', $this->templateParams( $this->getProfileEditForm() ) );
    }
    
    /**
     * Get Profile Picture
     * 
     * @param Request $request
     * @return Response
     */
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
            $profilePicture = '/build/default/images/avatar-1.jpg';
        }
        
        return new Response( $profilePicture, Response::HTTP_OK );
    }
    
    public function handleProfileFormAction( Request $request ): Response
    {
        $form   = $this->getProfileEditForm();
        $form->handleRequest( $request );
        if ( ! $form->isSubmitted() ) {
            throw new \Exception( "Profile Form is Not Submited Properly !" );
        }
        
        $em             = $this->doctrine->getManager();
        $oUser          = $form->getData();
        
        if ( ! $oUser->getPreferedLocale() ) {
            $oUser->setPreferedLocale( $request->getLocale() );
        }
        
        $oUserInfo          = $oUser->getInfo();
        $profilePictureFile = $form->get( 'profilePicture' )->getData();
        if ( $profilePictureFile ) {
            $this->createAvatar( $oUserInfo, $profilePictureFile );
        }
        
        $oUserInfo->setTitle( $form->get( 'title' )->getData() );
        $oUserInfo->setFirstName( $form->get( 'firstName' )->getData() );
        $oUserInfo->setLastName( $form->get( 'lastName' )->getData() );
        $oUserInfo->setDesignation( $form->get( 'designation' )->getData() );
        
        $oUserInfo->setUser( $oUser );
        $em->persist( $oUserInfo );
        $em->persist( $oUser );
        $em->flush();
        
        return $this->redirectToRoute( 'vs_users_profile_show' );
    }
    
    public function changeAvatarAction( Request $request ): Response
    {
        $em         = $this->doctrine->getManager();
        $oUser      = $this->getUser();
        $forms      = $this->getOtherForms();
        $f          = $forms['changeAvatarForm'];
        
        $f->handleRequest( $request );
        if ( ! $f->isSubmitted() ) {
            throw new \Exception( "Change Avatar Form is Not Submited Properly !" );
        }
        
        $oUserInfo          = $oUser->getInfo();
        $profilePictureFile = $f->get( 'profilePicture' )->getData();
        if ( $profilePictureFile ) {
            $this->createAvatar( $oUserInfo, $profilePictureFile );
        }
        
        $em->persist( $oUserInfo );
        $em->flush();
        
        return $this->redirectToRoute( 'vs_users_profile_show' );
    }
    
    public function changePasswordAction( Request $request ): Response
    {
        $em         = $this->doctrine->getManager();
        $oUser      = $this->getUser();
        $forms      = $this->getOtherForms();
        $f          = $forms['changePasswordForm'];
        
        $f->handleRequest( $request );
        if ( $f->isSubmitted() && $f->isValid() ) {
            $data           = $request->request->all();

            $oldPassword        = $data['change_password_form']['oldPassword'];
            $newPassword        = $data['change_password_form']['password']['first'];
            $newPasswordConfirm = $data['change_password_form']['password']['second'];
            
            if ( ! $this->userManager->isPasswordValid( $oUser, $oldPassword ) ) {
                throw new \Exception( 'Invalid Old Password !!!' );
            }
            
            if ( $newPassword !== $newPasswordConfirm ) {
                throw new \Exception( 'Passwords Not Equals !!!' );
            }
            
            $this->userManager->encodePassword( $oUser, $newPassword );
            $em->persist( $oUser );
            $em->flush();
            
            $this->vankosoftAgent->userPasswordChanged( $oUser, $oUser, $oldPassword, $newPassword );
        }
        
        return $this->redirectToRoute( 'vs_users_profile_show' );
    }
    
    protected function templateParams( $form )
    {
        return [
            'errors'                    => $form->getErrors( true, false ),
            'form'                      => $form->createView(),
            'user'                      => $this->getUser(),
            'otherForms'                => $this->getOtherForms(),
            
            'hasPaymentExtension'       => $this->hasExtension( self::EXTENSION_PAYMENT ),
            'hasSubscriptionsExtension' => $this->hasExtension( self::EXTENSION_USERSUBSCRIPTIONS ),
            'newsletterSubscriptions'   => $this->getNewsletterSubscriptions(),
            'paidSubscriptions'         => $this->getPaidSubscriptions(),
            'orders'                    => $this->getOrders(),
        ];
    }
    
    protected function getProfileEditForm()
    {
        $form       = $this->createForm( ProfileFormType::class, $this->getUser(), [
            'data'      => $this->getUser(),
            'action'    => $this->generateUrl( 'vs_users_profile_handle' ),
            'method'    => 'POST',
        ]);
        
        return $form;
    }
    
    protected function getOtherForms(): array
    {
        $changePasswordForm = $this->createForm( ChangePasswordFormType::class, null, [
            'action'    => $this->generateUrl( 'vs_users_profile_change_password' ),
            'method'    => 'POST',
        ]);
        
        $changeAvatarForm   = $this->createForm( ProfilePictureForm::class, null, [
            'action'    => $this->generateUrl( 'vs_users_profile_change_avatar' ),
            'method'    => 'POST',
        ]);
        
        return [
            'changePasswordForm'    => $changePasswordForm,
            'changeAvatarForm'      => $changeAvatarForm,
        ];
    }
    
    protected function hasExtension( $extension ): bool
    {
        return \array_key_exists( $extension, $this->getParameter( 'kernel.bundles' ) );
    }
    
    protected function getNewsletterSubscriptions()
    {
        $subscriptions  = [];
        if (
            $this->hasExtension ( self::EXTENSION_USERSUBSCRIPTIONS ) &&
            $this->getUser() instanceof \Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscribedUserInterface
        ) {
            try {
                //$subscriptions  = $this->getUser()->getSubscriptions( ( '\\' . $this->usersClass )::SUBSCRIPTION_TYPE_NEWSLETTER );
                $subscriptions  = [];
            } catch( \Doctrine\DBAL\Exception\TableNotFoundException $e ) {
                $subscriptions  = [];
            }
        }
        
        return $subscriptions;
    }
    
    protected function getPaidSubscriptions()
    {
        $subscriptions  = [];
        if (
            $this->hasExtension ( self::EXTENSION_PAYMENT ) &&
            $this->getUser() instanceof \Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscribedUserInterface
        ) {
            $subscriptions  = $this->getUser()->getPricingPlanSubscriptions();
        }
        
        return $subscriptions;
    }
    
    protected function getOrders()
    {
        $orders  = [];
        if (
            $this->hasExtension ( self::EXTENSION_PAYMENT ) &&
            $this->getUser() instanceof \Vankosoft\PaymentBundle\Model\Interfaces\PaymentsUserInterface
        ) {
            $orders = $this->getUser()->getOrders();
        }
            
        return $orders;
    }
    
    private function createAvatar( UserInfoInterface &$userInfo, File $file ): void
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
