<?php namespace VS\UsersBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use VS\ApplicationBundle\Component\Context\ApplicationContextInterface;
use VS\UsersBundle\Model\UserInterface;

class ModelVoter extends Voter
{
    // these strings are just invented: you can use anything
    const LIST      = 'list';
    const VIEW      = 'view';
    const EDIT      = 'edit';
    const REMOVE    = 'remove';
    
    private $security;
    
    private ApplicationContextInterface $applicationContext;
    
    /** @var Collection */
    private $disabledModels;
    
    public function __construct(
        ApplicationContextInterface $applicationContext,
        array $disabledModels,
        Security $security  = null
    ) {
            $this->applicationContext   = $applicationContext;
            $this->disabledModels       = new ArrayCollection( $disabledModels );
            $this->security             = $security;
    }
    
    protected function supports( string $attribute, $subject ): bool
    {
        if ( $this->disabledModels->isEmpty() ) {
            return self::ACCESS_ABSTAIN;
        }
        
        foreach ( $this->disabledModels as $role => $model ) {
            //return self::ACCESS_GRANTED;
            //return self::ACCESS_DENIED;
        }
        
        return false;
    }
    
    protected function voteOnAttribute( string $attribute, $subject, TokenInterface $token ): bool
    {
        $user = $token->getUser();
        
        if ( ! $user instanceof User ) {
            // the user must be logged in; if not, deny access
            return false;
        }
        
        // you know $subject is a Post object, thanks to `supports()`
        /** @var Post $post */
        $post = $subject;
        
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($post, $user);
            case self::EDIT:
                return $this->canEdit($post, $user);
        }
        
        throw new \LogicException('This code should not be reached!');
    }
    /*
    private function canView( Post $post, User $user ): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($post, $user)) {
            return true;
        }
        
        // the Post object could have, for example, a method `isPrivate()`
        return !$post->isPrivate();
    }
    
    private function canEdit(Post $post, User $user): bool
    {
        // this assumes that the Post object has a `getOwner()` method
        return $user === $post->getOwner();
    }
    */
}
