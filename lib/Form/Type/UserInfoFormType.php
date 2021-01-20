<?php namespace VS\UsersBundle\Form\Type;
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

class UserInfoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)    
    {
       
        $builder
            ->setMethod('PUT')
            //->add('apiKey', HiddenType::class)
            ->add('firstName', TextType::class, array('label' => 'registration.firstName', 'translation_domain' => 'IAUsersBundle'))
            ->add('lastName', TextType::class, array('label' => 'registration.lastName', 'translation_domain' => 'IAUsersBundle'))
            ->add('country', CountryType::class, array('label' => 'registration.yourCountry', 'translation_domain' => 'IAUsersBundle'))
            ->add('birthday', BirthdayType::class, array('label' => 'registration.yourBirthday', 'translation_domain' => 'IAUsersBundle', 'widget'=>'single_text'))
            ->add('occupation', TextType::class, array('label' => 'registration.occupation', 'translation_domain' => 'IAUsersBundle', 'required' => false))
            ->add('mobile', TextType::class, array('label' => 'registration.mobile', 'translation_domain' => 'IAUsersBundle', 'required' => false))
            ->add('website', UrlType::class, array('label' => 'registration.websiteUrl', 'translation_domain' => 'IAUsersBundle', 'required' => false))
            
            ->add('btnSave', SubmitType::class, array('label' => 'Save'))
            ->add('btnCancel', ButtonType::class, array('label' => 'Cancel'))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            //'inherit_data' => true
            'data_class' => UserInfo::class
        ));
    }
}
