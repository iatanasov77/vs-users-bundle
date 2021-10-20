<?php namespace VS\UsersBundle\Controller;

use VS\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends AbstractCrudController //ResourceController
{
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $userManager    = $this->container->get( 'vs_users.manager.user' );
        $userManager->encodePassword( $entity, $entity->getPassword() );
        
        $roles  = $form->get( "roles_options" )->getData();
        $entity->setRoles( $roles );
        //var_dump( $roles ); die;
        
//         $entity->setVerified( true );
        
//         // I dont know yet if these fields should be in the form
//         $entity->setPreferedLocale( $request->getLocale() );
//         $entity->setEnabled( true );
    }
}
