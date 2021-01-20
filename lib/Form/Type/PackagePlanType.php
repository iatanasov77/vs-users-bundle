<?php namespace VS\UsersBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PackagePlanType extends AbstractType
{

    public function getName()
    {
        return 'FormPackagePlan';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add( 'plan', EntityType::class, array(
                'class' => 'VS\UsersBundle\Entity\Plan',
                'choice_label' => 'title',
                'required' => false
            ))
            ->add('price', MoneyType::class)
            ->add('description', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VS\UsersBundle\Entity\PackagePlan'
        ));
    }

}

