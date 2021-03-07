<?php namespace VS\UsersBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use VS\UsersBundle\Model\UserInterface;

class ProfileFormType extends UserFormType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        parent::buildForm( $builder, $options );
        
        $builder->remove( 'roles_options' );
        $builder->remove( 'password' );
        $builder->remove( 'email' );
        $builder->remove( 'username' );
        
        $builder
            ->setMethod( 'POST' )
            
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
        return 'vs_users.profile';
    }
}
