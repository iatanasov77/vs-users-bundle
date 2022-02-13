<?php namespace Vankosoft\UsersBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Vankosoft\ApplicationBundle\Model\Application;
use Vankosoft\UsersBundle\Model\UserInterface;
use Vankosoft\UsersBundle\Component\UserRole;

class UserFormType extends AbstractForm
{
    protected $requestStack;
    
    protected $applicationClass;
    
    protected $auth;
    
    public function __construct(
        RequestStack $requestStack,
        string $dataClass,
        string $applicationClass,
        AuthorizationCheckerInterface $auth
    ) {
        parent::__construct( $dataClass );
        
        $this->requestStack     = $requestStack;
        $this->applicationClass = $applicationClass;
        $this->auth             = $auth;
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->setMethod( 'PUT' )
            //->add('apiKey', HiddenType::class)
  
            ->add( 'enabled', CheckboxType::class, [
                'label' => 'vs_users.form.user.enabled',
                'translation_domain'    => 'VSUsersBundle',
            ])
            
            ->add( 'verified', CheckboxType::class, [
                'label' => 'vs_users.form.user.verified',
                'translation_domain'    => 'VSUsersBundle',
            ])
            ->add( 'prefered_locale', ChoiceType::class, [
                'label'                 => 'vs_users.form.user.prefered_locale',
                'translation_domain'    => 'VSUsersBundle',
                'choices'               => \array_flip( \Vankosoft\ApplicationBundle\Component\I18N::LanguagesAvailable() ),
                'data'                  => $this->requestStack->getCurrentRequest()->getLocale(),
            ])
        
            ->add( 'email', TextType::class, [
                'label' => 'vs_users.form.user.email',
                'translation_domain' => 'VSUsersBundle'
            ])
            ->add( 'username', TextType::class, [
                'label' => 'vs_users.form.user.username',
                'translation_domain' => 'VSUsersBundle'
            ])
            
            ->add( 'plain_password', RepeatedType::class, [
                'type'                  => PasswordType::class,
                'label'                 => 'vs_users.form.user.password',
                'translation_domain'    => 'VSUsersBundle',
                'first_options'         => ['label' => 'vs_users.form.user.password'],
                'second_options'        => ['label' => 'vs_users.form.user.password_repeat'],
                "mapped"                => false,
            ])
            
            // https://symfony.com/doc/current/security.html#hierarchical-roles
            ->add( 'roles_options', ChoiceType::class, [
                'label'                 => 'vs_users.form.user.roles',
                'translation_domain'    => 'VSUsersBundle',
                "mapped"                => false,
                "multiple"              => true,
                'choices'               => UserRole::choices()
            ])
            
            ->add( 'applications', EntityType::class, [
                'label'                 => 'vs_users.form.user.applications_allowed',
                'translation_domain'    => 'VSUsersBundle',
                'class'                 => $this->applicationClass,
                'choice_label'          => 'title',
                "required"              => false,
                //"mapped"                => false,
                "multiple"              => true,
                'query_builder' => function( EntityRepository $repository ) {
                    $qb = $repository->createQueryBuilder( 'app' );
                    if( $this->auth->isGranted( 'ROLE_SUPER_ADMIN' ) ) {
                        return $qb;
                    } else {
                        return $qb
                            ->where( $qb->expr()->neq( 'app.code', '?appCode' ) )
                            ->setParameter( 'appCode', 'admin-panel' )
                        ;
                    }
                },
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
        return 'vs_users.user';
    }
}

