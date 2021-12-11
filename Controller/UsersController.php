<?php namespace VS\UsersBundle\Controller;

use VS\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class UsersController extends AbstractCrudController //ResourceController
{
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $plainPassword  = $form->get( "plain_password" )->getData();
        if ( $plainPassword ) {
            $userManager    = $this->container->get( 'vs_users.manager.user' );
            $userManager->encodePassword( $entity, $plainPassword );
        }
        
        $selectedRoles  = \json_decode( $request->request->get( 'selectedRoles' ), true );
        $this->buildRoles( $entity, $selectedRoles );
        /*
        $roles  = $form->get( "roles_options" )->getData();
        var_dump( $roles ); die;
        $entity->setRoles( $roles );
        */
        
        /*
        $entity->setVerified( true );

        // I dont know yet if these fields should be in the form
        $entity->setPreferedLocale( $request->getLocale() );
        $entity->setEnabled( true );
        */
        
//         $allowedApplications    = $form->get( "applications" )->getData();
//         foreach ( $allowedApplications as $app ) {
//             $entity->addApplication( $app );
//         }
    }
    
    private function buildRoles( &$entity, array $roles )
    {
        //var_dump( $roles ); die;
        $repo   = $this->get( 'vs_users.repository.user_roles' );
        
        $entity->setRolesCollection( new ArrayCollection() );
        foreach ( $roles as $r ) {
            $entity->addRole( $repo->find( $r['id'] ) );
        }
    }
}
