<?php  namespace VS\UsersBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use VS\ApplicationBundle\Controller\AbstractCrudController;
use VS\ApplicationBundle\Controller\TaxonomyHelperTrait;

/**
 * Documentation
 * --------------
 * http://atlantic18.github.io/DoctrineExtensions/doc/tree.html
 *
 * Good example
 * -------------
 * http://drib.tech/programming/hierarchical-data-relational-databases-symfony-4-doctrine
 * https://github.com/dribtech/hierarchical-data-tutorial-part-2
 */
class UsersRolesController extends AbstractCrudController
{
    use TaxonomyHelperTrait;
    
    protected function customData( Request $request ): array
    {
        $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
            $this->getParameter( 'vs_application.user_roles.taxonomy_code' )
        );
        
        return [
            'taxonomyId'    => $taxonomy ? $taxonomy->getId() : 0,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $translatableLocale     = $form['currentLocale']->getData();
        $categoryName           = $form['name']->getData();
        $parentCategory         = $this->get( 'vs_users.repository.user_roles' )
                                        ->findByTaxonId( $request->request->get( 'parent' ) ); // $_POST['user_role_form']['parent']
        
        if ( $entity->getTaxon() ) {
            $entity->getTaxon()->setCurrentLocale( $translatableLocale );
            $entity->getTaxon()->setName( $categoryName );
            if ( $parentCategory ) {
                $entity->getTaxon()->setParent( $parentCategory->getTaxon() );
            }
            
            $entity->setParent( $parentCategory );
        } else {
            /*
             * @WORKAROUND Create Taxon If not exists
             */
            $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                $this->getParameter( 'vs_application.user_roles.taxonomy_code' )
            );
            $newTaxon   = $this->createTaxon(
                $categoryName,
                $translatableLocale,
                $parentCategory ? $parentCategory->getTaxon() : null,
                $taxonomy->getId()
            );
            
            $entity->setTaxon( $newTaxon );
            $entity->setParent( $parentCategory );
    }
}
