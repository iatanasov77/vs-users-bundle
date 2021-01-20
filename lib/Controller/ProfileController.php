<?php namespace VS\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use VS\UsersBundle\Form\ProfileFormType;

class ProfileController extends Controller
{
    public function showAction( Request $request )
    {
        $form = $this->createForm( ProfileFormType::class, $this->getUser()->getUserInfo() );
        
        /*
         * Fetch Available Packages
         */
        $pr                     = $this->getDoctrine()->getRepository( 'IAUsersBundle:Package' );
        $packages               = $pr->findAll();
        
        $paymentMethods         = $this->getDoctrine()->getRepository( 'IAPaymentBundle:PaymentMethod' )->findAll();
        //var_dump( $this->container->getParameter( 'ia_payment.accounts' ) ); die;
        $subscriptionDetails    = $this->userSubscriptionDetails();
        
        return $this->render( '@IAUsers/Profile/show.html.twig', [
            'user'                  => $this->getUser(),
            'subscription'          => $this->getUser()->getSubscription(),
            'subscriptionDetails'   => $subscriptionDetails,
            'form'                  => $form->createView(),
            'packages'              => $packages,
            'paymentMethods'        => $paymentMethods
        ]);
    }
    
    private function userSubscriptionDetails()
    {
        $sr             = $this->getDoctrine()->getRepository( 'IAUsersBundle:UserSubscription' );
        $subscription   = $this->getUser()->getSubscription();
        
        return [
            'active'    => $sr->isActive( $subscription )
        ];
    }
}

