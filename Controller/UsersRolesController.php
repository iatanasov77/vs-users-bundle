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
    
    protected function customData( Request $request, $entity = null ): array
    {
        $taxonomyCode   = $this->getParameter( 'vs_application.user_roles.taxonomy_code' );
        $taxonomy       = $this->get( 'vs_application.repository.taxonomy' )->findByCode( $taxonomyCode );
        if ( ! $taxonomy ) {
            throw new \Exception( sprintf( "Taxonomy with code '%s' does not exists. Please create it before!", $taxonomyCode ) );
        }
        
        return [
            'taxonomy'  => $taxonomy,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $translatableLocale = $form['currentLocale']->getData();
        $categoryName       = $form['name']->getData();
        
        // Try This to Get Post Values
        //echo "<pre>"; var_dump( $request->request->all() ); die;
        $parentRole         = $this->get( 'vs_users.repository.user_roles' )
                                    ->findByTaxonId( $_POST['user_role_form']['parent'] );
        
        if ( $entity->getTaxon() ) {
            $entity->getTaxon()->setCurrentLocale( $translatableLocale );
            $entity->getTaxon()->setName( $categoryName );
            if ( $parentRole ) {
                $entity->getTaxon()->setParent( $parentRole->getTaxon() );
            }
            
            $entity->setParent( $parentRole );
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
                $parentRole ? $parentRole->getTaxon() : null,
                $taxonomy->getId()
            );
            
            $entity->setTaxon( $newTaxon );
            $entity->setParent( $parentRole );
        }
    }
}
