<?php namespace Vankosoft\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerBuilder;

use Vankosoft\UsersBundle\Entity\User;

class ApiController extends AbstractController
{
    public function indexAction()
    {
        // Get and decode post paarams
        $params = $this->get('request')->getContent();
        $params = json_decode($params);
        
        // Prepare Repository Params
        $searchArray = isset($params->search)   ? 
                        array($params->search->key => $params->search->val) :
                        array();
        $orderArray = array($params->orderBy=>$params->orderDir);
        $page = $params->page;
        $ipp = $params->ipp;

        // Query Repository
        $er = $this->getDoctrine()->getRepository('IAUsersBundle:User');
        $entities = $er->findBy($searchArray, $orderArray, $ipp, ($page-1)*$ipp);
        $response = array(
                'countTotal' => $er->countTotal(),
                'entities' => $entities
            );

        // Serialize and return response
        $serializer = SerializerBuilder::create()->build();
        $response = $serializer->serialize($response, 'json');

        return new Response($response);
    }

    public function detailAction($id)
    {
        if(intval($id)) {
            $er = $this->getDoctrine()->getRepository('IAUsersBundle:User');
            $contact = $er->find($id);
        } else {
            $contact = new User();
        }

        $serializer = SerializerBuilder::create()->build();
        $json = $serializer->serialize($contact, 'json');

        return new Response($json);
    }

    public function saveAction()
    {
    	$data = $this->get("request")->getContent();

        $serializer = SerializerBuilder::create()->build();
        $contact = $serializer->deserialize($data, 'Vankosoft\UsersBundle\Entity\User', 'json');
        $phones = $contact->getPhones();

        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->merge($contact);
        
        /**
         * This not right but i don't know how to do
         */
        foreach($phones as $k => $phone) {
            $phone->setContact($contact);
            if(!$phone->getCreated()) {
                $phone->setCreated(new \DateTime());
                $phone->setModified(new \DateTime());
            }
            
            $entityManager->merge($phone);
        }
        $entityManager->flush();
        
        $response = array(
            'message' => 'SUCCESS!!!'
        );
        return new Response(json_encode($response));
    }

    public function deleteAction($id)
    {
        if(intval($id)) {
            $er = $this->getDoctrine()->getRepository('IAUsersBundle:User');
            $contact = $er->find($id);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contact);
            $entityManager->flush();
        }
        
        $response = array(
            'message' => 'SUCCESS!!!'
        );
        return new Response(json_encode($response));
    }


}
