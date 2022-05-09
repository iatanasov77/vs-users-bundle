<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Generator\VerifyEmailTokenGenerator;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\UsersBundle\Form\RegistrationFormType;
use Vankosoft\UsersBundle\Model\UserInterface;

class RegisterController extends AbstractController
{
    /**
     * @var UserManager
     */
    private $userManager;
    
    /**
     * @var RepositoryInterface
     */
    private $usersRepository;
    
    /**
     * @var Factory
     */
    private $usersFactory;
    
    /**
     * @var RepositoryInterface
     */
    private $userRolesRepository;
    
    /**
     * @var MailerInterface
     */
    private $mailer;
    
    /**
     * @var VerifyEmailHelperInterface
     */
    private $verifyEmailHelper;
    
    /**
     * Needed to generate Api Token
     * 
     * @var VerifyEmailHelperInterface
     */
    private $tokenGenerator;
    
    /**
     * @var RepositoryInterface
     */
    private $pagesRepository;

    public function __construct(
        UserManager $userManager,
        RepositoryInterface $usersRepository,
        Factory $usersFactory,
        RepositoryInterface $userRolesRepository,
        MailerInterface $mailer,
        RepositoryInterface $pagesRepository
    ) {
            $this->userManager          = $userManager;
            $this->usersRepository      = $usersRepository;
            $this->usersFactory         = $usersFactory;
            $this->userRolesRepository  = $userRolesRepository;
            $this->mailer               = $mailer;
            $this->pagesRepository      = $pagesRepository;
    }
    
    public function setTokenGenerator( VerifyEmailTokenGenerator $tokenGenerator ) : void
    {
        $this->tokenGenerator   = $tokenGenerator;
    }
    
    /**
     * Used from service to set helper because so can to hellper to be optional, how it is explained here:
     * https://symfony.com/doc/current/service_container/optional_dependencies.html
     *
     * @param VerifyEmailHelperInterface $helper
     */
    public function setVerifyEmailHelper( VerifyEmailHelperInterface $helper ) : void
    {
        $this->verifyEmailHelper    = $helper;
    }
    
    public function index( Request $request, MailerInterface $mailer ) : Response
    {
        $em         = $this->getDoctrine()->getManager();
        $oUser      = $this->usersFactory->createNew();
        $form       = $this->createForm( RegistrationFormType::class, $oUser, [
            'data'      => $oUser,
            'action'    => $this->generateUrl( 'vs_users_register_form' ),
            'method'    => 'POST',
        ]);
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formUser       = $form->getData();
            $plainPassword  = $form->get( "plain_password" )->getData();
            $oUser          = $this->userManager->createUser(
                $formUser->getUsername(),
                $formUser->getEmail(),
                $plainPassword
            );
            //$oUser->setApiToken( $this->tokenGenerator->createToken( strval( time() ), $oUser->getEmail() ) );
            
            //$oUser->setRoles( [$request->request->get( 'registerRole' )] );
            $oUser->addRole( $this->userRolesRepository->findByTaxonCode( $this->getParameter( 'vs_users.register_role' ) ) );
            
            $oUser->setPreferedLocale( $request->getLocale() );
            $oUser->setVerified( false );
            $oUser->setEnabled( false );
            
            $em->persist( $oUser );
            $em->flush();
            
            $this->sendConfirmationMail( $oUser, $mailer );
            
            return $this->redirectToRoute( $this->getParameter( 'vs_users.default_redirect' ) );
        }
        
        $termsPage  = $this->pagesRepository->findBySlug( 'terms-and-conditions' );
        
        return $this->render( '@VSUsers/Register/register.html.twig', [
            'errors' => $form->getErrors( true, false ),
            'form'  => $form->createView(),
            'termsPageTitle'    => $termsPage ? $termsPage->getTitle() : null,
            'termsPageContent'  => $termsPage ? $termsPage->getText() : null,
        ]);
    }
    
    public function verify( Request $request ): Response
    {
//         $this->denyAccessUnlessGranted( 'IS_AUTHENTICATED_FULLY' );
//         $user = $this->getUser();
        $id = $request->get( 'id' ); // retrieve the user id from the url
        // Verify the user id exists and is not null
        if( null === $id ) {
            return $this->redirectToRoute( $this->getParameter( 'vs_users.default_redirect' ) );
        }

        $user = $this->usersRepository->find( $id );

        // Ensure the user exists in persistence
        if ( null === $user ) {
            return $this->redirectToRoute( $this->getParameter( 'vs_users.default_redirect' ) );
        }
                
        try {
            $this->verifyEmailHelper->validateEmailConfirmation( $request->getUri(), $user->getId(), $user->getEmail() );
        } catch ( VerifyEmailExceptionInterface $e ) {
            $this->addFlash( 'verify_email_error', $e->getReason() );
            
            return $this->redirectToRoute( 'vs_users_register_form' );
        }
        
        // Mark your user as verified.
        $user->setVerified( true );
        $user->setEnabled( true );
        $this->getDoctrine()->getManager()->persist( $user );
        $this->getDoctrine()->getManager()->flush();
        
        $this->addFlash( 'success', 'Your e-mail address has been verified.' );
        
        return $this->redirectToRoute( $this->getParameter( 'vs_users.default_redirect' ) );
    }
    
    private function sendConfirmationMail( UserInterface $oUser, MailerInterface $mailer )
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
                                    'vs_users_register_confirmation',
                                    $oUser->getId(),
                                    $oUser->getEmail(),
                                    ['id' => $oUser->getId()]
                                );
        
        $email = ( new TemplatedEmail() )
                ->from( $this->getParameter( 'mailer_user' ) )
                ->to( $oUser->getEmail() )
                ->htmlTemplate( '@VSUsers/Register/confirmation_email.html.twig' )
                ->context([
                    'signedUrl'             => $signatureComponents->getSignedUrl(),
                    'expiresAtMessageKey'   => $signatureComponents->getExpirationMessageKey(),
                    'expiresAtMessageData'  => $signatureComponents->getExpirationMessageData(),
                ]);
        
        $mailer->send( $email );
    }
}
