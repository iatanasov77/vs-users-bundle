<?php namespace Vankosoft\UsersBundle\Form\Traits;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vankosoft\UsersBundle\Component\UserInfo;

trait UserInfoFormTrait
{
    public function buildUserInfoForm( FormBuilderInterface &$builder, array $options ): void
    {
        $builder
            ->add( 'profilePicture', FileType::class, [
                'label'                 => 'vs_users.form.profile.picture_lable',
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => $options['profilePictureMapped'],
                
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
            
            ->add( 'title', ChoiceType::class, [
                'label'                 => 'vs_users.form.user.title',
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => $options['titleMapped'],
                'choices'               => UserInfo::choices(),
            ])
            
            ->add( 'firstName', TextType::class, [
                'label'                 => 'vs_users.form.user.firstName',
                'attr'                  => ['placeholder' => 'vs_users.form.user.firstName_placeholder'],
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => $options['firstNameMapped'],
            ])
            
            ->add( 'lastName', TextType::class, [
                'label'                 => 'vs_users.form.user.lastName',
                'attr'                  => ['placeholder' => 'vs_users.form.user.lastName_placeholder'],
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => $options['lastNameMapped'],
            ])
            
            ->add( 'designation', TextType::class, [
                'label'                 => 'vs_users.form.profile.designation',
                'attr'                  => ['placeholder' => 'vs_users.form.profile.designation'],
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => $options['designationMapped'],
            ])
        ;
    }
}
