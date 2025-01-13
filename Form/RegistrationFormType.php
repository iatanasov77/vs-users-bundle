<?php namespace Vankosoft\UsersBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

/*
 * Form Inheritance:
 * https://stackoverflow.com/questions/22414166/inherit-form-or-add-type-to-each-form
 */
class RegistrationFormType extends UserFormType
{
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository,
        RequestStack $requestStack,
        string $applicationClass,
        AuthorizationCheckerInterface $auth,
        array $requiredFields
    ) {
        parent::__construct( $dataClass, $localesRepository, $requestStack, $applicationClass, $auth, $requiredFields );
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder->remove( 'enabled' );
        $builder->remove( 'verified' );
        
        $builder->remove( 'roles_options' );
        $builder->remove( 'applications' );
        
        $builder->remove( 'btnSave' );
        
        $builder
            ->setMethod( 'POST' )
            ->add( 'registerRole', HiddenType::class, ['data' => 'ROLE_USER', 'mapped' => false] )
            ->add( 'agreeTerms', CheckboxType::class, [
                'label'                 => 'vs_users.form.registration.agreement_text',
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => false,
            ])
            ->add( 'btnRgister', SubmitType::class, [
                'label' => 'vs_users.form.registration.register',
                'translation_domain' => 'VSUsersBundle'
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {   
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection' => false,
            ])
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
