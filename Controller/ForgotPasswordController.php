<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Vankosoft\UsersBundle\Model\UserInterface;
use Vankosoft\UsersBundle\Repository\ResetPasswordRequestRepository;

class ForgotPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;
    
    /**
     * @var ResetPasswordHelperInterface
     */
    private $resetPasswordHelper;
    
    /**
     * @var ResetPasswordRequestRepository
     */
    private $repository;
    
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
    
    public function __construct( ResetPasswordRequestRepository $repository )
    {
        $this->repository           = $repository;
    }
    
    public function indexAction( Request $request, MailerInterface $mailer ) : Response
    {
        if ( $request->isMethod( 'POST' ) ) {
            $email  = $request->request->get( 'email' );
            $user   = $this->getRepository()->findOneBy( ['email' => $email] );
            if ( ! $user ) {
                $this->addFlash( 'error', 'This email not found !' );
                return $this->redirectToRoute( 'app_login' );
            }

            $this->addFlash( 'notice', 'Email sent with link to reset your password !' );
            $this->sendMail( $user, $mailer );
            
            return $this->redirectToRoute( 'app_login' );
        }
        
        return $this->render( '@VSUsers/Resetting/forgot_password.html.twig' );
    }
    
    public function resetAction( string $token, Request $request ) : Response
    {
        $this->repository->setContainer( $this->container );
        $oUser   = $this->resetPasswordHelper->validateTokenAndFetchUser( $token );
        
        if ( $request->isMethod( 'POST' ) ) {
            $userManager    = $this->container->get( 'vs_users.manager.user' );
            
            $password           = $request->request->get( 'password' );
            $passwordConfirm    = $request->request->get( 'password_confirm' );
            if ( $password === $passwordConfirm ) {
                $em = $this->getDoctrine()->getManager();
                
                $userManager->encodePassword( $oUser, $password );
                $em->persist( $oUser );
                $em->flush();
                
                $this->resetPasswordHelper->removeResetRequest( $token );
                
                return $this->redirectToRoute( 'app_login' ); // Success change password ;)
            }
        }
        
        return $this->render( '@VSUsers/Resetting/change_password.html.twig', [
            'user'  => $oUser,
            'token' => $token,
        ]);
    }
    
    protected function getRepository()
    {
        return $this->get( 'vs_users.repository.users' );
    }
    
    private function sendMail( UserInterface $oUser, MailerInterface $mailer )
    {
        $this->repository->setContainer( $this->container );
        
        $resetToken = $this->resetPasswordHelper->generateResetToken( $oUser );
        $resetUrl   = $this->generateUrl(
                        'vs_users_forgot_password_reset',
                        [token => $resetToken->getToken()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
        
        $email = ( new TemplatedEmail() )
                    ->from( $this->container->getParameter( 'mailer_user' ) )
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
