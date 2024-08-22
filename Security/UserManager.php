<?php namespace Vankosoft\UsersBundle\Security;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vankosoft\CmsBundle\Component\Uploader\FileUploaderInterface;

use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Model\UserInfoInterface;

class UserManager
{
    private $userFactory;
    private $userRepository;
    private $entityManager;
    private $encoderFactory;
    private $userInfoFactory;
    
    /** @var FactoryInterface */
    private $avatarImageFactory;
    
    /** @var FileUploaderInterface */
    private $imageUploader;
    
    public function __construct(
        FactoryInterface $userFactory,
        EntityRepository $userRepository,
        EntityManager $entityManager,
        PasswordHasherFactoryInterface $encoderFactory,
        FactoryInterface $userInfoFactory,
        FactoryInterface $avatarImageFactory,
        FileUploaderInterface $imageUploader
    ) {
        $this->userFactory      = $userFactory;
        $this->userRepository   = $userRepository;
        $this->entityManager    = $entityManager;
        $this->encoderFactory   = $encoderFactory;
        $this->userInfoFactory  = $userInfoFactory;
        $this->avatarImageFactory   = $avatarImageFactory;
        $this->imageUploader        = $imageUploader;
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
    
    public function createAvatar( UserInfoInterface &$userInfo, File $file ): void
    {
        $avatarImage    = $userInfo->getAvatar() ?: $this->avatarImageFactory->createNew();
        $uploadedFile   = new UploadedFile( $file->getRealPath(), $file->getBasename() );
        
        $avatarImage->setFile( $uploadedFile );
        $this->imageUploader->upload( $avatarImage );
        $avatarImage->setFile( null ); // reset File Because: Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed
        
        if ( ! $userInfo->getAvatar() ) {
            $userInfo->setAvatar( $avatarImage );
        }
    }
}
