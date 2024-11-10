<?php namespace Vankosoft\UsersBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Vankosoft\CmsBundle\Model\Interfaces\FileInterface;

final class AvatarImageRepository extends EntityRepository implements AvatarImageRepositoryInterface
{
    public function findOneByOwnerId( string $ownerId ): ?FileInterface
    {
        return $this->createQueryBuilder( 'o' )
            ->andWhere( 'o.owner = :ownerId' )
            ->setParameter( 'ownerId', $ownerId )
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
