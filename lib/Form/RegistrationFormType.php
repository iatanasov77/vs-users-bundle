<?php

namespace VS\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use VS\UsersBundle\Form\Type\ProfileType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('username');
        $builder
            ->add('profile', new ProfileType(), array(
                'label' => false,
            ))
            ->add('btnRegister', 'submit', array('label' => 'registration.createAccount', 'translation_domain' => 'IAUsersBundle'))
            ->add('btnCancel', 'reset', array('label' => 'form.cancel', 'translation_domain' => 'IAUsersBundle'))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VS\UsersBundle\Entity\User',
            'csrf_token_id' => 'registration',
            // BC for SF < 2.8
            //'intention'  => 'registration',
            'allow_extra_fields' => true,
            'data_class' => 'VS\UsersBundle\Entity\User',
        ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        //return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'ia_user_registration';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
