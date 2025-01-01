<?php namespace Vankosoft\UsersBundle\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;

use Vankosoft\CmsBundle\Model\Interfaces\FileInterface;

interface AvatarImageRepositoryInterface extends RepositoryInterface
{
    public function findOneByOwnerId( string $id ): ?FileInterface;
}
