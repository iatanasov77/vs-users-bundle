<?php namespace Vankosoft\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vankosoft\UsersBundle\Model\UserInfo;

class UserInfoForm extends AbstractType
{
    use Traits\UserInfoFormTrait;
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $this->buildUserInfoForm( $builder );
        $builder->setMethod( 'POST' );
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'data_class' => UserInfo::class,
        ]);
    }
}
