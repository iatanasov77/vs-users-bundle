<?php namespace VS\UsersBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use VS\UsersBundle\Model\UserInterface;

class ProfileFormType extends UserFormType
{
    public function __construct( RequestStack $requestStack, string $dataClass )
    {
        parent::__construct( $requestStack, $dataClass );
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        parent::buildForm( $builder, $options );
        
        $builder->remove( 'enabled' );
        $builder->remove( 'verified' );
        
        $builder->remove( 'roles_options' );
        $builder->remove( 'password' );
        $builder->remove( 'email' );
        $builder->remove( 'username' );
        
        $builder->setMethod( 'POST' );
        
        $builder
            ->add( 'profilePicture', FileType::class, [
                'label'                 => 'vs_users.form.profile.picture_lable',
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => false,
                
                // make it optional so you don't have to re-upload the Profile Image
                // every time you edit the Profile details
                'required'              => false,
                
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints'           => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'vs_users.form.profile.picture_info',
                    ])
                ],
            ])
            
            ->add( 'firstName', TextType::class, [
                'label'                 => 'vs_users.form.user.firstName',
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => false,
            ])
            ->add( 'lastName', TextType::class, [
                'label'                 => 'vs_users.form.user.lastName',
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => false,
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
