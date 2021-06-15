<?php namespace VS\UsersBundle\Security;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
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
        PasswordHasherFactoryInterface $encoderFactory
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
    
    /**
     * @NOTE Symfony 4
     */
//     public function encodePassword( UserInterface &$user, $plainPassword )
//     {
//         $encoder    = $this->encoderFactory->getEncoder( $user );
        
//         $salt       = md5( time() );
//         $pass       = $encoder->encodePassword( $plainPassword, $salt );
        
//         $user->setPassword( $pass );
//         $user->setSalt( $salt );
//     }
    
    /**
     * @NOTE Symfony 5
     */
    public function encodePassword( UserInterface &$user, $plainPassword )
    {
        $hasher         = $this->encoderFactory->getPasswordHasher( $user );
        
        // Using PasswordHasher
        //$hashedPassword = $hasher->hashPassword( $user, $plainPassword );
        
        // Using MigratingPasswordHasher
        $salt           = md5( time() );
        $hashedPassword = $hasher-> hash( $plainPassword, $salt );
        
        $user->setPassword( $hashedPassword );
        $user->setSalt( $salt );
    }
    
    public function isPasswordValid( UserInterface $user, $plainPassword )
    {
        $encoder    = $this->encoderFactory->getEncoder( $user );
        
        return $encoder->isPasswordValid( $user->getPassword(), $plainPassword, $user->getSalt() );
    }
}

