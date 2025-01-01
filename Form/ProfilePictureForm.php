<?php namespace Vankosoft\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ProfilePictureForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->setMethod( 'POST' )
            
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
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_users.profile.change_avatar';
    }
}