<?php namespace VS\UsersBundle\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;

use VS\CmsBundle\Model\ImageInterface;

interface AvatarImageRepositoryInterface extends RepositoryInterface
{
    public function findOneByOwnerId( string $id ): ?ImageInterface;
}
