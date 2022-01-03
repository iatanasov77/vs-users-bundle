<?php namespace Vankosoft\UsersBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use Vankosoft\UsersBundle\Model\UserInterface;

class ProfileFormType extends UserFormType
{
    use Traits\UserInfoFormTrait;
    
    public function __construct( RequestStack $requestStack, string $dataClass, string $applicationClass )
    {
        parent::__construct( $requestStack, $dataClass, $applicationClass );
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        parent::buildForm( $builder, $options );
        
        $this->buildUserInfoForm( $builder );
        $builder->setMethod( 'POST' );
        
        $builder->remove( 'enabled' );
        $builder->remove( 'verified' );
        $builder->remove( 'roles_options' );
        $builder->remove( 'applications' );
        $builder->remove( 'plain_password' );
        $builder->remove( 'email' );
        $builder->remove( 'username' );
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
        return 'vs_users.profile';
    }
}
