<?php namespace VS\UsersBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;

use VS\UsersBundle\Form\Type\PackagePlanType;

class PackageFormType extends AbstractResourceType implements ContainerAwareInterface
{

    use ContainerAwareTrait;
    
    public function __construct($container = null)
    {
        $this->container = $container;
    }
    
    public function getName()
    {
        return 'ia_paid_membership_packages';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
        ->add('enabled', CheckboxType::class, array('label' => 'Enabled'))
        ->add('title', TextType::class, array('label' => 'Title'))
        ->add('plans', CollectionType::class, array(
                'entry_type'   => PackagePlanType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'by_reference' => false
            ))
            ->add('btnSave', SubmitType::class, array('label' => 'Save'))
            ->add('btnCancel', ButtonType::class, array('label' => 'Cancel'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'VS\UsersBundle\Entity\Package'
        ));
    }

}
