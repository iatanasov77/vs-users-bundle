<?php namespace VS\UsersBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


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
        
        $builder->setMethod( 'POST' );
        
        $builder
            ->add( 'profilePicture', FileType::class, [
                'label' => 'Profile picture',
                'mapped' => false,
                
                // make it optional so you don't have to re-upload the Profile Image
                // every time you edit the Profile details
                'required' => false,
                
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
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
        return 'vs_users.profile';
    }
}
