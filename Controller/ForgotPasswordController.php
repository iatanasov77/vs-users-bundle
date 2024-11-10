<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Repository\ResetPasswordRequestRepository;
use Vankosoft\UsersBundle\Security\UserManager;
use Vankosoft\UsersBundle\Form\ChangePasswordFormType;
use Vankosoft\UsersBundle\Form\ForgotPasswordForm;

class ForgotPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;
    
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /**
     * @var ResetPasswordHelperInterface
     */
    protected $resetPasswordHelper;
    
    /**
     * @var ResetPasswordRequestRepository
     */
    protected $repository;
    
    /**
     * @var RepositoryInterface
     */
    protected $usersRepository;
    
    /**
     * @var MailerInterface
     */
    protected $mailer;
    
    /**
     * @var UserManager
     */
    protected $userManager;
    
    /**
     * @var array
     */
    protected $params;
    
    public function __construct(
        ManagerRegistry $doctrine,
        ResetPasswordRequestRepository $repository,
        RepositoryInterface $usersRepository,
        MailerInterface $mailer,
        Factory $resetPasswordRequestFactory,
        UserManager $userManager,
        array $parameters
    ) {
        $this->doctrine             = $doctrine;
        $this->repository           = $repository;
        $this->usersRepository      = $usersRepository;
        $this->mailer               = $mailer;
        $this->userManager          = $userManager;
        $this->params               = $parameters;
        
        $this->repository->setRequestFactory( $resetPasswordRequestFactory );
    }
    
    /**
     * Used from service to set helper because so can to hellper to be optional, how it is explained here:
     * https://symfony.com/doc/current/service_container/optional_dependencies.html
     *
     * @param ResetPasswordHelperInterface $helper
     */
    public function setResetPasswordHelper( ResetPasswordHelperInterface $helper ) : void
    {
        $this->resetPasswordHelper  = $helper;
    }
    
    public function indexAction( Request $request, MailerInterface $mailer ) : Response
    {
        $form   = $this->getForgotPasswordForm();
        $form->handleRequest( $request );
        if (  $form->isSubmitted() ) {
            $email  = $form->get( 'email' )->getData();
            $user   = $this->usersRepository->findOneBy( ['email' => $email] );
            if ( ! $user ) {
                $this->addFlash( 'error', 'This email not found !' );
                return $this->redirectToRoute( 'app_login' );
            }

            try {
                $this->sendMail( $user, $mailer );
                $this->addFlash( 'notice', 'Email sent with link to reset your password !' );
            } catch ( TooManyPasswordRequestsException $e ) {
                $this->addFlash( 'notice', 'TooManyPasswordRequestsException !' );
            }
            
            return $this->redirectToRoute( 'app_login' );
        }
        
        return $this->render( '@VSUsers/Resetting/forgot_password.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
    
    public function resetAction( string $token, Request $request ) : Response
    {
        $tokenExpired   = false;
        try {
            $oUser   = $this->resetPasswordHelper->validateTokenAndFetchUser( $token );
        } catch ( ExpiredResetPasswordTokenException $e ) {
            $tokenExpired   = true;
        }
        
        $form   = $this->getChangePasswordForm( $token );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() && ! $tokenExpired ) {
            $password   = $form->get( "password" )->getData();
            
            $em         = $this->doctrine->getManager();
            $this->userManager->encodePassword( $oUser, $password );
            $em->persist( $oUser );
            $em->flush();
            
            $this->resetPasswordHelper->removeResetRequest( $token );
            
            return $this->redirectToRoute( 'app_login' ); // Success change password ;)
        }
        
        return $this->render( '@VSUsers/Resetting/change_password.html.twig', [
            'user'  => $oUser,
            'token' => $token,
            'form'  => $form->createView(),
        ]);
    }
    
    protected function getForgotPasswordForm()
    {
        $form   = $this->createForm( ForgotPasswordForm::class, null, [
            'action'    => $this->generateUrl( 'vs_users_forgot_password_form' ),
        ]);
        
        return $form;
    }
    
    protected function getChangePasswordForm( string $token )
    {
        $form   = $this->createForm( ChangePasswordFormType::class, null, [
            'action'    => $this->generateUrl( 'vs_users_forgot_password_reset', ['token' => $token] ),
        ]);
        
        return $form;
    }
    
    protected function sendMail( UserInterface $oUser, MailerInterface $mailer )
    {
        $resetToken = $this->resetPasswordHelper->generateResetToken( $oUser );
        $resetUrl   = $this->generateUrl(
                        'vs_users_forgot_password_reset',
                        ['token' => $resetToken->getToken()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
        
        $email = ( new TemplatedEmail() )
                    ->from( $this->params['mailerUser'] )
                    ->to( $oUser->getEmail() )
                    ->htmlTemplate( '@VSUsers/Resetting/forgot_password_email.html.twig' )
                    ->context([
                        'resetUrl'  => $resetUrl,
                        'expiresAt' => $resetToken->getExpiresAt()->format( 'Y-m-d H:i:s' )
                    ]);
        
        //var_dump( $email->getHtmlBody() ); die;
        $mailer->send( $email );
    }
}
