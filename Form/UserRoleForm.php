<?php namespace Vankosoft\UsersBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Vankosoft\UsersBundle\Model\UserRole;
use Vankosoft\UsersBundle\Model\Interfaces\UserRoleInterface;

class UserRoleForm extends AbstractForm
{
    /** @var string */
    protected $roleClass;
    
    /** @var RepositoryInterface */
    protected $repository;
    
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository,
        RequestStack $requestStack,
        RepositoryInterface $repository
    ) {
        parent::__construct( $dataClass );
        
        $this->roleClass            = $dataClass;
        
        $this->localesRepository    = $localesRepository;
        $this->requestStack         = $requestStack;
        
        $this->repository           = $repository;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $role           = $options['data'];
        $formMethod     = $role && $role->getId() ? 'PUT' : 'POST';
        $currentLocale  = $this->requestStack->getCurrentRequest()->getLocale();
        
        $builder
            ->setMethod( $formMethod )
            
            ->add( 'currentLocale', ChoiceType::class, [
                'label'                 => 'vs_users.form.user_role.locale',
                'translation_domain'    => 'VSUsersBundle',
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
            
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_users.form.user_role.name',
                'translation_domain'    => 'VSUsersBundle',
            ])
            
            ->add( 'role', TextType::class, [
                'label'                 => 'vs_users.form.user_role.role',
                'translation_domain'    => 'VSUsersBundle',
            ])
            
            ->add( 'description', TextType::class, [
                'label'                 => 'vs_users.form.user_role.description',
                'translation_domain'    => 'VSUsersBundle',
                'required'              => false,
            ])
            
            ->add( 'parent', EntityType::class, [
                'label'                 => 'vs_users.form.user_role.parent_role',
                'translation_domain'    => 'VSUsersBundle',
                'class'                 => $this->roleClass,
                'query_builder'         => function ( EntityRepository $er ) use ( $role )
                {
                    $qb = $er->createQueryBuilder( 'ur' );
                    if  ( $role && $role->getId() ) {
                        $qb
                            ->where( 'ur.id != :id' )->setParameter( 'id', $role->getId() )
                            ->orWhere( 'ur.role != :anonymousRole' )->setParameter( 'anonymousRole', UserRole::ANONYMOUS )
                        ;
                    }
                    
                    return $qb;
                },
                'choice_label'  => 'name',
                
                'required'      => false,
                'placeholder'   => 'vs_users.form.user_role.parent_role_placeholder',
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
                'user_roles',
            ])
            ->setAllowedTypes( 'user_roles', UserRoleInterface::class )
        ;
    }
    
    public function getName()
    {
        return 'vs_users.user_role';
    }
}
