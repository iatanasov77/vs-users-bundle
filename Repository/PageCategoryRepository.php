<?php namespace VS\CmsBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository as ResourceRepository;
use Sylius\Component\Resource\Model\ResourceInterface;

class PageCategoryRepository extends NestedTreeRepository implements RepositoryInterface
{
    protected $resourceRepository;
    
    public function __construct( $em, $class )
    {
        $this->resourceRepository = new ResourceRepository( $em, $class );
    }
    
    public function countPages( $categoryId )
    {
        $countPages = $this->find( $categoryId )->getPages()->count();
        
        return $countPages;
    }
    
    public function createPaginator(array $criteria = [], array $sorting = []): iterable
    {
        return $this->resourceRepository( $criteria, $sorting );
    }
    
    public function add( ResourceInterface $resource ): void
    {
        $this->resourceRepository->add( $resource );
    }
    
    public function remove(ResourceInterface $resource ): void
    {
        $this->resourceRepository->remove( $resource );
    }
}
