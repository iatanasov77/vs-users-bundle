<?php namespace VS\UsersBundle\Security;

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
    
    public function createUser( $username, $email, $plainPassword ) : UserInterface
    {
        //if ( Assert::notNull( $this->userRepository->findOneByEmail( $username ) ) ) {
        if ( is_object( $this->userRepository->findOneByEmail( $username ) ) ) {
            throw new \Exception( 'User exists !!!' );
        }
        
        $user       = $this->userFactory->createNew();
        
        $user->setEmail( $email );
        $user->setUsername( $username );
        
        $this->encodePassword( $user, $plainPassword );
        
        return $user;
    }
    
    public function saveUser( UserInterface $user )
    {
        $this->entityManager->persist( $user );
        $this->entityManager->flush();
    }
    
    public function encodePassword( UserInterface &$user, $plainPassword )
    {
        $encoder    = $this->encoderFactory->getEncoder( $user );
        $salt       = md5( time() );
        $pass       = $encoder->encodePassword( $plainPassword, $salt );
        
        $user->setPassword( $pass );
        $user->setSalt( $salt );
    }
    
    public function isPasswordValid( UserInterface $user, $plainPassword )
    {
        $encoder    = $this->encoderFactory->getEncoder( $user );
        
        return $encoder->isPasswordValid( $user->getPassword(), $plainPassword, $user->getSalt() );
    }
}

