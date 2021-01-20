<?php namespace VS\UsersBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Payum\Paypal\ExpressCheckout\Nvp\Api;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use VS\UsersBundle\Form\Type\PackagePlanType;

class PlanFormType extends AbstractType
{

    public function getName()
    {
        return 'ia_paid_membership_plans';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
        ->add('enabled', CheckboxType::class, array('label' => 'Enabled'))
        ->add('title', TextType::class, array('label' => 'Title'))
                
        ->add('subscriptionPeriod', ChoiceType::class, 
                    array('label'=>'Subscription Period', 
                        'choices'=>array(
                            ''                           => ' -- Choose a period -- ',
                            Api::BILLINGPERIOD_DAY       => 'Day',
                            Api::BILLINGPERIOD_WEEK      => 'Week',
                            Api::BILLINGPERIOD_SEMIMONTH => 'Semi Month',
                            Api::BILLINGPERIOD_MONTH     => 'Month',
                            Api::BILLINGPERIOD_YEAR      => 'Year'
                        )
                    )
            )
            
            ->add('btnSave', SubmitType::class, array('label' => 'Save'))
            ->add('btnCancel', ButtonType::class, array('label' => 'Cancel'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
//        $resolver->setDefaults(array(
//            'data_class' => 'IA\Bundle\WebContentThiefBundle\Entity\Fieldset'
//        ));
    }

}
