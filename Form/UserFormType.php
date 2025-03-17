<?php namespace Vankosoft\UsersBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Vankosoft\ApplicationBundle\Model\Application;
use Vankosoft\ApplicationBundle\Repository\ApplicationRepository;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Component\UserRole;

class UserFormType extends AbstractForm
{
    /** @var string */
    protected $applicationClass;
    
    /** @var string */
    protected $userRolesClass;
    
    /** @var AuthorizationCheckerInterface */
    protected $auth;
    
    /** @var array */
    protected $requiredFields;
    
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository,
        RequestStack $requestStack,
        string $applicationClass,
        string $userRolesClass,
        AuthorizationCheckerInterface $auth,
        array $requiredFields
    ) {
        parent::__construct( $dataClass );
        
        $this->localesRepository    = $localesRepository;
        $this->requestStack         = $requestStack;
        
        $this->applicationClass     = $applicationClass;
        $this->userRolesClass       = $userRolesClass;
        $this->auth                 = $auth;
        
        $this->requiredFields       = $requiredFields;
    }

    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity         = $builder->getData();
        $currentLocale  = $entity->getPreferedLocale() ?: $this->requestStack->getCurrentRequest()->getLocale();
        
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
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'placeholder'           => 'vs_users.form.user.prefered_locale_placeholder',
                'required'              => false,
            ])
        
            ->add( 'email', EmailType::class, [
                'label'                 => 'vs_users.form.user.email',
                'attr'                  => ['placeholder' => 'vs_users.form.user.email_placeholder'],
                'translation_domain'    => 'VSUsersBundle'
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
                'required'              => is_null( $entity->getId() ),
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
                'query_builder' => function( ApplicationRepository $repository ) {
                    $qb = $repository->createQueryBuilder( 'app' );
                    if( $this->auth->isGranted( 'ROLE_SUPER_ADMIN' ) ) {
                        return $qb;
                    } else {
                        return $qb
                            ->where( $qb->expr()->neq( 'app.code', ':appCode' ) )
                            ->setParameter( 'appCode', 'admin-panel' )
                        ;
                    }
                },
            ])
            
            ->add( 'allowedRoles', EntityType::class, [
                'label'                 => 'vs_application.form.allowed_roles_label',
                'translation_domain'    => 'VSApplicationBundle',
                'class'                 => $this->userRolesClass,
                'choice_label'          => 'role',
                "required"              => false,
                "mapped"                => false,
                "multiple"              => true,
//                 'query_builder' => function( ApplicationRepository $repository ) {
//                     $qb = $repository->createQueryBuilder( 'app' );
//                     if( $this->auth->isGranted( 'ROLE_SUPER_ADMIN' ) ) {
//                         return $qb;
//                     } else {
//                         return $qb
//                             ->where( $qb->expr()->neq( 'app.code', ':appCode' ) )
//                             ->setParameter( 'appCode', 'admin-panel' )
//                         ;
//                     }
//                 },
            ])
        ;
        
        /**
         * Fixing Symfony\Component\Form\Exception\TransformationFailedException
         *          'The selected choice is invalid.'
         */
        $builder->addEventListener( FormEvents::PRE_SUBMIT, function( FormEvent $event ): void {
            $data   = $event->getData();
            if ( ! isset( $data['roles_options'] ) ) {
                return;    
            }
            
            $form           = $event->getForm();
            $rolesOptions   = $data['roles_options'];
            if( $rolesOptions ) {
                $form->add( 'roles_options', ChoiceType::class, [
                    'choices'   => [],
                    "mapped"    => false,
                ]);
            }
        });
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
                'validation_groups' => false,   // 'roles_options' The selected choice is invalid.
            ])
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

