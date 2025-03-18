<?php namespace Vankosoft\UsersBundle\Controller;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class UsersController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        return [
            'displaySiblings'  => $this->getParameter( 'vs_users.crud.display_siblings' ),
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $plainPassword  = $form->get( "plain_password" )->getData();
        if ( $plainPassword ) {
            $userManager    = $this->container->get( 'vs_users.manager.user' );
            $userManager->encodePassword( $entity, $plainPassword );
            
            $currentUser    = $this->get( 'vs_users.security_bridge' )->getUser();
            $this->get( 'vs_agent.agent' )->userPasswordChanged(
                $currentUser,
                $entity,
                'UNKNOWN OLD PASSWORD',
                $plainPassword
            );
        }
        
        $this->buildUserInfo( $entity, $form );
        
        $selectedRoles  = \json_decode( $request->request->get( 'selectedRoles' ), true );
        $this->buildRoles( $entity, $selectedRoles );
        
        $allowedApplications    = $form->get( "applications" )->getData();
        $this->clearApplications( $entity );
        $entity->setApplications( $allowedApplications );
        
        $currentUser = $this->get( 'vs_users.security_bridge' )->getUser();
        if ( ! $currentUser->hasRole( 'ROLE_SUPER_ADMIN' ) ) {
            $entity->setAllowedRoles( $currentUser->getAllowedRoles() );
        }
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
    
    /**
     * Used before setApplications method to fix when removing an application
     * MANUAL: https://stackoverflow.com/questions/38955114/symfony-doctrine-remove-manytomany-association/38955917
     */
    private function clearApplications( &$entity )
    {
        $userApps   = $entity->getApplications();
        $appRepo    = $this->get( 'vs_application.repository.application' );
        
        foreach ( $appRepo->findAll() as $app ) {
            if ( ! $userApps->contains( $app ) ) {
                $app->removeUser( $entity );
            }
        }
        
        return $this;
    }
    
    private function buildUserInfo( &$entity, &$form )
    {
        if ( ! $entity->getInfo() ) {
            $userInfo   = $this->get( 'vs_users.factory.user_info' )->createNew();
            
            // May Be First and Last Name Should Be Added to Create User Form
            $userInfo->setFirstName( 'NOT' );
            $userInfo->setLastName( 'EDITED' );
            
            $this->getDoctrine()->getManager()->persist( $userInfo );
            $entity->setInfo( $userInfo );
        }
    }
}
