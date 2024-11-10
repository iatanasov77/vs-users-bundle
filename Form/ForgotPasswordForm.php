<?php namespace Vankosoft\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class ForgotPasswordForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->setMethod( 'POST' )
            
            ->add( 'email', EmailType::class, [
                'label'                 => 'vs_users.form.forgot_password.email_label',
                'attr'                  => ['placeholder' => 'vs_users.form.forgot_password.email_placeholder'],
                'translation_domain'    => 'VSUsersBundle'
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label'                 => 'vs_users.form.forgot_password.submit_button',
                'translation_domain'    => 'VSUsersBundle'
            ])
            ->add( 'btnCancel', ButtonType::class, [
                'label'                 => 'vs_users.form.user.cancel',
                'translation_domain'    => 'VSUsersBundle'
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => true,
            ])
        ;
        
    }
    
    public function getName()
    {
        return 'vs_users.forgot_password';
    }
}