<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

use Vankosoft\UsersBundle\Model\UserInterface;

class UserManager
{
    private $userFactory;
    private $userRepository;
    private $entityManager;
    private $encoderFactory;
    private $userInfoFactory;
    
    public function __construct(
        FactoryInterface $userFactory,
        EntityRepository $userRepository,
        EntityManager $entityManager,
        PasswordHasherFactoryInterface $encoderFactory,
        FactoryInterface $userInfoFactory
    ) {
        $this->userFactory      = $userFactory;
        $this->userRepository   = $userRepository;
        $this->entityManager    = $entityManager;
        $this->encoderFactory   = $encoderFactory;
        $this->userInfoFactory  = $userInfoFactory;
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
        $user->setInfo( $this->userInfoFactory->createNew() );
        $this->encodePassword( $user, $plainPassword );
        
        /**
         * This should be Implemented
         */
        $user->setApiToken( 'NOT_IMPLEMETED' );
        
        return $user;
    }
    
    public function saveUser( UserInterface $user )
    {
        $this->entityManager->persist( $user );
        $this->entityManager->flush();
    }
    
    public function encodePassword( UserInterface &$user, $plainPassword )
    {
        $hasher         = $this->encoderFactory->getPasswordHasher( $user );
        
        // Using PasswordHasher
        //$hashedPassword = $hasher->hashPassword( $user, $plainPassword );
        
        // Using MigratingPasswordHasher
        $salt           = md5( time() );
        $hashedPassword = $hasher->hash( $plainPassword, $salt );
        
        $user->setPassword( $hashedPassword );
        $user->setSalt( $salt );
    }
    
    public function isPasswordValid( UserInterface $user, $plainPassword )
    {
        $encoder    = $this->encoderFactory->getPasswordHasher( $user );
        
        return $encoder->verify( $user->getPassword(), $plainPassword, $user->getSalt() );
    }
}
