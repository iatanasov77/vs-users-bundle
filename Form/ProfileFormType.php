<?php namespace Vankosoft\UsersBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

class ProfileFormType extends UserFormType
{
    use Traits\UserInfoFormTrait;
    
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
        
        $this->buildUserInfoForm( $builder, $options );
        $builder->setMethod( 'POST' );
        
        $builder->remove( 'enabled' );
        $builder->remove( 'verified' );
        $builder->remove( 'roles_options' );
        $builder->remove( 'applications' );
        $builder->remove( 'plain_password' );
        $builder->remove( 'email' );
        $builder->remove( 'username' );
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefined([
                'users',
            ])
            ->setAllowedTypes( 'users', UserInterface::class )
            
            ->setDefaults([
                'csrf_protection'       => false,
                'profilePictureMapped'  => false,
                'titleMapped'           => false,
                'firstNameMapped'       => false,
                'lastNameMapped'        => false,
                'designationMapped'     => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_users.profile';
    }
}
