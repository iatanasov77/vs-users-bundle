<?php namespace VS\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use VS\UsersBundle\Entity\UserInfo;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add( 'apiToken', HiddenType::class )
            ->add( 'firstName', TextType::class, [
                'label'                 => 'registration.firstName',
                'translation_domain'    => 'VSUsersBundle'
            ])
            ->add( 'lastName', TextType::class, [
                'label'                 => 'registration.lastName',
                'translation_domain'    => 'VSUsersBundle'
            ])
            ->add( 'country', CountryType::class, [
                'label'                 => 'registration.yourCountry',
                'translation_domain'    => 'VSUsersBundle'   
            ])
            ->add( 'birthday', BirthdayType::class, [
                'label'                 => 'registration.yourBirthday',
                'translation_domain'    => 'VSUsersBundle',
                'widget'                => 'single_text'
            ])
            ->add( 'occupation', TextType::class, [
                'label'                 => 'registration.occupation',
                'translation_domain'    => 'VSUsersBundle',
                'required'              => false
            ])
            ->add( 'mobile', TelType::class, [
                'label'                 => 'registration.mobile',
                'translation_domain'    => 'VSUsersBundle',
                 'required'             => false
            ])
            ->add( 'website', UrlType::class, [
                'label'                 => 'registration.websiteUrl',
                'translation_domain'    => 'VSUsersBundle',
                'required'              => false
            ])
            
            ->add( 'btnSave', SubmitType::class, [
                'label'                 => 'form.save',
                'translation_domain'    => 'VSUsersBundle'
            ])
            ->add( 'btnCancel', ButtonType::class, [
                'label'                 => 'form.cancel',
                'translation_domain'    => 'VSUsersBundle'
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            //'inherit_data' => true,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'data_class' => UserInfo::class
        ));
    }
}
