<?php namespace VS\UsersBundle\Component\Manager;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

use VS\UsersBundle\Model\UserInterface;

class UserManager
{
    private $userFactory;
    private $userRepository;
    private $entityManager;
    private $encoderFactory;
    
    public function __construct(
        FactoryInterface $userFactory,
        EntityRepository $userRepository,
        EntityManager $entityManager,
        EncoderFactory $encoderFactory
    ) {
        $this->userFactory      = $userFactory;
        $this->userRepository   = $userRepository;
        $this->entityManager    = $entityManager;
        $this->encoderFactory   = $encoderFactory;
    }
    
    public function createUser( $username, $email, $password ) : UserInterface
    {
        //if ( Assert::notNull( $this->userRepository->findOneByEmail( $username ) ) ) {
        if ( is_object( $this->userRepository->findOneByEmail( $username ) ) ) {
            throw new \Exception( 'User exists !!!' );
        }
        
        $user       = $this->userFactory->createNew();
        $encoder    = $this->encoderFactory->getEncoder( $user );
        
        $salt       = md5( time() );
        $pass       = $encoder->encodePassword( $password, $salt );
        
        $user->setEmail( $email );
        $user->setUsername( $username );
        $user->setPassword( $pass );
        $user->setSalt( $salt );
        
        return $user;
    }
    
    public function saveUser( UserInterface $user )
    {
        $this->entityManager->persist( $user );
        $this->entityManager->flush();
    }
}

