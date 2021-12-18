<?php namespace VS\ApplicationBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use VS\ApplicationBundle\Form\SettingsForm;

class SettingsController extends ResourceController
{
    public function indexAction( Request $request ): Response
    {
        $forms          = [];
        $applications    = $this->getApplicationRepository()->findAll();
        
        $configuration  = $this->requestConfigurationFactory->create( $this->metadata, $request );
        
        $er             = $this->getRepository();
        $factory        = $this->getFactory();
        $settings       = $er->getSettings();
        
        $oSettings      = $settings ?: $factory->createNew();
        $forms[]        = $this->resourceFormFactory->create( $configuration, $oSettings )->createView();
        
        foreach( $applications as $app ) {
            $settings       = $er->getSettings( $app );
            $oSettings      = $settings ?: $factory->createNew();
            $forms[]        = $this->resourceFormFactory->create( $configuration, $oSettings )->createView();
        }
        
//         $form->handleRequest( $request );
//         if( $form->isSubmitted() && $form->isValid() ) {
//             $em = $this->getDoctrine()->getManager();
//             $em->persist( $form->getData() );
//             $em->flush();
            
//             return $this->redirect( $this->generateUrl( 'vs_application_settings' ) );
//         }
        
        $taxonomyPagesCategories    = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                                            $this->getParameter( 'vs_application.page_categories.taxonomy_code' )
                                        );
        return $this->render( '@VSApplication/Pages/Settings/index.html.twig', [
            'forms'         => $forms,
            'applications'  => $applications,
            'pcTaxonomyId'  => $taxonomyPagesCategories ? $taxonomyPagesCategories->getId() : 0,
        ]);
    }
    
    protected function getRepository()
    {
        return $this->get( 'vs_application.repository.settings' );
    }
    
    protected function getFactory()
    {
        return $this->get( 'vs_application.factory.settings' );
    }
    
    protected function getApplicationRepository()
    {
        return $this->get( 'vs_application.repository.application' );
    }
}