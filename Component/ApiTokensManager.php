<?php namespace Vankosoft\UsersBundle\Component;

use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use Vankosoft\UsersBundle\Model\UserInterface;

class ApiTokensManager
{
    /**
     * @var VerifyEmailHelperInterface
     */
    private $verifyEmailHelper;
    
    public function __construct( VerifyEmailHelperInterface $helper )
    {
        $this->verifyEmailHelper    = $helper;
    }
    
    public function getVerifySignature( UserInterface $oUser, string $signatureRoute ): VerifyEmailSignatureComponents
    {
        $signature  = $this->verifyEmailHelper->generateSignature(
            $signatureRoute,
            $oUser->getId(),
            $oUser->getEmail(),
            ['id' => $oUser->getId()]
        );
        
        return $signature;
    }
    
    public function verifySignature( string $signedUrl, string $userId, string $userEmail ): bool
    {
        $this->verifyEmailHelper->validateEmailConfirmation( $signedUrl, $userId, $userEmail );
        
        return true;
    }
}
