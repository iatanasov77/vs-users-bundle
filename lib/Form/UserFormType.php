<?php namespace VS\UsersBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use VS\UsersBundle\Form\Type\UserInfoFormType;
use VS\UsersBundle\Model\UserInterface;
use VS\UsersBundle\Component\UserRole;

class UserFormType extends AbstractResourceType  implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    public function __construct( $container = null )
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->setMethod( 'PUT' )
            //->add('apiKey', HiddenType::class)
            //->add('enabled', CheckboxType::class, array('label' => 'Enabled'))
  
            ->add( 'email', TextType::class, [
                'label' => 'vs_users.user.email',
                'translation_domain' => 'VSUsersBundle'
            ])
            ->add( 'username', TextType::class, [
                'label' => 'vs_users.user.username',
                'translation_domain' => 'VSUsersBundle'
            ])
            
            ->add( 'password', RepeatedType::class, [
                'type'                  => PasswordType::class,
                'label'                 => 'vs_users.user.password',
                'translation_domain'    => 'VSUsersBundle',
                'first_options'         => ['label' => 'vs_users.user.password'],
                'second_options'        => ['label' => 'vs_users.user.password_repeat'],
            ])
            
            ->add( 'firstName', TextType::class, [
                'label'                 => 'vs_users.user.firstName',
                'translation_domain'    => 'VSUsersBundle'
            ])
            ->add( 'lastName', TextType::class, [
                'label'                 => 'vs_users.user.lastName',
                'translation_domain'    => 'VSUsersBundle'
            ])
            
            // https://symfony.com/doc/current/security.html#hierarchical-roles
            ->add( 'roles_options', ChoiceType::class, [
                'label'                 => 'vs_users.user.roles',
                'translation_domain'    => 'VSUsersBundle',
                "mapped"                => false,
                "multiple"              => true,
                'choices'               => UserRole::choices()
            ])
            
            ->add( 'btnSave', SubmitType::class, [
                'label' => 'vs_users.user.save',
                'translation_domain' => 'VSUsersBundle'
            ])
            ->add( 'btnCancel', ButtonType::class, [
                'label' => 'vs_users.user.cancel',
                'translation_domain' => 'VSUsersBundle'
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
            
            ->setDefaults([
                'data_class' => UserInterface::class,
            ])
        ;
    }

    public function getName()
    {
        return 'vs_users.user';
    }
}

