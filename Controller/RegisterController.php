<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Generator\VerifyEmailTokenGenerator;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use Vankosoft\UsersBundle\Security\UserManager;
use Vankosoft\UsersBundle\Form\RegistrationFormType;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Security\AnotherLoginFormAuthenticator;

class RegisterController extends AbstractController
{
    /** @var UserAuthenticatorInterface */
    protected $guardHandler;
    
    /** @var AnotherLoginFormAuthenticator */
    protected $authenticator;
    
    /** @var ApplicationContextInterface */
    protected $applicationContext;
    
    /** @var UserManager */
    protected $userManager;
    
    /** @var RepositoryInterface */
    protected $usersRepository;
    
    /** @var Factory */
    protected $usersFactory;
    
    /** @var RepositoryInterface */
    protected $userRolesRepository;
    
    /** @var MailerInterface */
    protected $mailer;
    
    /** @var VerifyEmailHelperInterface */
    protected $verifyEmailHelper;
    
    /**
     * Needed to generate Api Token
     * 
     * @var VerifyEmailTokenGenerator
     */
    protected $tokenGenerator;
    
    /** @var RepositoryInterface */
    protected $pagesRepository;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var TranslatorInterface */
    protected $translator;
    
    /** @var array */
    protected $params;

    public function __construct(
        ManagerRegistry $doctrine,
        TranslatorInterface $translator,
        ApplicationContextInterface $applicationContext,
        UserManager $userManager,
        RepositoryInterface $usersRepository,
        Factory $usersFactory,
        RepositoryInterface $userRolesRepository,
        MailerInterface $mailer,
        RepositoryInterface $pagesRepository,
        //GuardAuthenticatorHandler $guardHandler,
        UserAuthenticatorInterface $guardHandler,
        AnotherLoginFormAuthenticator $authenticator,
        array $parameters
    ) {
        $this->doctrine             = $doctrine;
        $this->translator           = $translator;
        $this->applicationContext   = $applicationContext;
        $this->userManager          = $userManager;
        $this->usersRepository      = $usersRepository;
        $this->usersFactory         = $usersFactory;
        $this->userRolesRepository  = $userRolesRepository;
        $this->mailer               = $mailer;
        $this->pagesRepository      = $pagesRepository;
        
        $this->guardHandler         = $guardHandler;
        $this->authenticator        = $authenticator;
        
        $this->params               = $parameters;
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
    
    public function index( Request $request, MailerInterface $mailer ): Response
    {
        try {
            if ( $this->getUser() ) {
                return $this->redirectToRoute( $this->params['defaultRedirect'] );
            }
        } catch ( \LogicException $e ) {
            
        }
        
        $form   = $this->getForm();
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $em             = $this->doctrine->getManager();
            $formUser       = $form->getData();
            $plainPassword  = $form->get( "plain_password" )->getData();
            $oUser          = $this->userManager->createUser(
                $formUser->getUsername(),
                $formUser->getEmail(),
                $plainPassword
            );
            
            $oUser->addRole( $this->userRolesRepository->findByTaxonCode( $this->params['registerRole'] ) );
            $oUser->addApplication( $this->applicationContext->getApplication() );
            
            $oUser->setPreferedLocale( $request->getLocale() );
            $oUser->setVerified( false );
            $oUser->setEnabled( false );
            
            $em->persist( $oUser );
            $em->flush();
            
            $this->sendConfirmationMail( $oUser, $mailer );
            
            $this->addFlash(
                'success',
                $this->translator->trans( 'vs_application.form.register.alert_success', [], 'VSApplicationBundle' )
            );
            
            return $this->redirectToRoute( $this->params['defaultRedirect'] );
        }
        
        return $this->render( '@VSUsers/Register/register.html.twig', $this->templateParams( $form ) );
    }
    
    public function verify( Request $request ): Response
    {
        $id = $request->get( 'id' ); // retrieve the user id from the url
        // Verify the user id exists and is not null
        if( null === $id ) {
            return $this->redirectToRoute( $this->params['defaultRedirect'] );
        }

        $user = $this->usersRepository->find( $id );
        // Ensure the user exists in persistence
        if ( null === $user ) {
            return $this->redirectToRoute( $this->params['defaultRedirect'] );
        }
                
        try {
            $this->verifyEmailHelper->validateEmailConfirmationFromRequest( $request, $user->getId(), $user->getEmail() );
        } catch ( VerifyEmailExceptionInterface $e ) {
            $this->addFlash( 'verify_email_error', $e->getReason() );
            
            return $this->redirectToRoute( 'vs_users_register_form' );
        }
        
        // Mark your user as verified.
        $user->setVerified( true );
        $user->setEnabled( true );
        $this->doctrine->getManager()->persist( $user );
        $this->doctrine->getManager()->flush();
        
        $this->addFlash(
            'success',
            $this->translator->trans( 'vs_application.form.register.alert_verified', [], 'VSApplicationBundle' )
        );
        
        if ( $this->params['loginAfterVerify'] ) {
            return $this->guardHandler->authenticateUser(
                $user,
                $this->authenticator,
                $request
            );
        }
        
        return $this->redirectToRoute( $this->params['defaultRedirect'] );
    }
    
    protected function getForm()
    {
        $oUser      = $this->usersFactory->createNew();
        $form       = $this->createForm( $this->params['registrationForm'], $oUser, [
            'data'      => $oUser,
            'action'    => $this->generateUrl( 'vs_users_register_form' ),
            'method'    => 'POST',
        ]);
        
        return $form;
    }
    
    protected function templateParams( $form )
    {
        $termsPage  = $this->pagesRepository->findBySlug( 'terms-and-conditions' );
        
        return [
            'errors'            => $form->getErrors( true, false ),
            'form'              => $form->createView(),
            'termsPageTitle'    => $termsPage ? $termsPage->getTitle() : null,
            'termsPageContent'  => $termsPage ? $termsPage->getText() : null,
        ];
    }
    
    protected function sendConfirmationMail( UserInterface $oUser, MailerInterface $mailer )
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
                                    'vs_users_register_confirmation',
                                    $oUser->getId(),
                                    $oUser->getEmail(),
                                    ['id' => $oUser->getId()]
                                );
        
        $email = ( new TemplatedEmail() )
                ->from( $this->params['mailerUser'] )
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
