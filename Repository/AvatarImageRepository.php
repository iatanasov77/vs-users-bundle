<?php namespace VS\UsersBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use VS\CmsBundle\Model\FileInterface;

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
