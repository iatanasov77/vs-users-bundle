<?php namespace VS\UsersBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use VS\UsersBundle\Model\UserInterface;

/*
 * Form Inheritance:
 * https://stackoverflow.com/questions/22414166/inherit-form-or-add-type-to-each-form
 */
class RegistrationFormType extends UserFormType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        parent::buildForm( $builder, $options );
        
        $builder->remove( 'roles_options' );
        $builder->remove( 'btnSave' );
        
        $builder
            ->setMethod( 'POST' )
            ->add( 'registerRole', HiddenType::class, ['data' => 'ROLE_USER', 'mapped' => false] )
            ->add( 'agreeTerms', CheckboxType::class, [
                'label'                 => 'vs_users.form.registration.agree_terms',
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => false,
            ])
            ->add( 'btnRgister', SubmitType::class, [
                'label' => 'vs_users.form.registration.register',
                'translation_domain' => 'VSUsersBundle'
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ) : void
    {   
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefined([
                'users',
            ])
            ->setAllowedTypes( 'users', UserInterface::class )
        ;
    }

    public function getName()
    {
        return 'vs_users.registration';
    }
}
