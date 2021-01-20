<?php

namespace VS\UsersBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Url\Url as SpatieUrl;

use VS\UsersBundle\Entity\User;
use VS\UsersBundle\Entity\UserInfo;
use VS\UsersBundle\Form\UserFormType;
use VS\UsersBundle\Form\Type\UserInfoFormType;

class UsersController extends ResourceController
{
    public function indexAction( Request $request ): Response
    {
        return $this->render('IAUsersBundle:UsersCrud:index.html.twig', [
            'users' => $this->getDoctrine()->getRepository( 'VS\UsersBundle\Entity\User' )->findAll()
        ]);
    }
    
    public function createAction( Request $request ): Response
    {
        //$id = Url::ProjectsUrlGetId();
        $id = $this->getId();
        
        $er = $this->getDoctrine()->getRepository( 'VS\UsersBundle\Entity\User' );
        $user = $id ? $er->find($id) : new User();
        
        $form = $this->createForm( UserFormType::class, $user );
        
        //if($form->isSubmitted()) {
        if($request->isMethod('POST') || $request->isMethod('PUT')) {
            $form->handleRequest($request);
            $formUser   = $form->getData();
            
            $email   = $formUser->getEmail();
            $username   = $formUser->getUserName();
            $password   = $formUser->getPassword();
            
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->createUser();
            
            //$repository = $em->getRepository( 'VS\UsersBundle\Entity\UserGroup' );
            //$group = $repository->findOneByName( 'SomeGroup' );
            //$user->addGroup( $group );
            
            $user->setUsername( $username );
            $user->setEmail( $email );
            $user->setEmailCanonical( $email );
            //$user->setLocked( 0 ); // don't lock the user
            $user->setEnabled( 1 ); // enable the user or enable it later with a confirmation token in the email
            // this method will encrypt the password with the default settings :)
            $user->setPlainPassword( $password );
            $userManager->updateUser( $user );
            
            return $this->redirect($this->generateUrl('ia_users_users_crud_index'));
        }
        
        $tplVars = array(
            'form'      => $form->createView(),
            'item'      => $user,
        );
        return $this->render('IAUsersBundle:UsersCrud:create.html.twig', $tplVars);
    }
    
    public function updateAction( Request $request ) : Response
    {
        //$id = Url::ProjectsUrlGetId();
        $id = $this->getId();
        
        $er = $this->getDoctrine()->getRepository( 'VS\UsersBundle\Entity\User' );
        $user = $id ? $er->find($id) : new User();
        
        //$form = $this->createForm( UserFormType::class, $user );
        $form = $this->createForm( UserInfoFormType::class, $user->getUserInfo() );
        
        //if($form->isSubmitted()) {
        if($request->isMethod('POST') || $request->isMethod('PUT')) {
            $form->handleRequest($request);
            $userInfo   = $form->getData();
            $user->setUserInfo( $userInfo );
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist( $user );
            $entityManager->flush();
            
            return $this->redirect($this->generateUrl('ia_users_users_update', ['id' =>$id]));
        }
        
        $tplVars = array(
            'form'      => $form->createView(),
            'item'      => $user,
        );
        return $this->render( 'IAUsersBundle:UsersCrud:create.html.twig', $tplVars );
    }
    
    protected function getId()
    {
        $url = SpatieUrl::fromString( $_SERVER['REQUEST_URI'] );
        return intval( $url->getSegment( 2 ) );
    }
    
}
