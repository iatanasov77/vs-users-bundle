<?php namespace Vankosoft\UsersBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Vankosoft\UsersBundle\Model\AvatarImage;
use Vankosoft\UsersBundle\Repository\AvatarImageRepository;

use Vankosoft\UsersBundle\Model\User;
use Vankosoft\UsersBundle\Controller\UsersController;
use Vankosoft\UsersBundle\Repository\UsersRepository;
use Vankosoft\UsersBundle\Form\UserFormType;

use Vankosoft\UsersBundle\Model\UserRole;
use Vankosoft\UsersBundle\Repository\UserRolesRepository;
use Vankosoft\UsersBundle\Controller\UsersRolesController;
use Vankosoft\UsersBundle\Form\UserRoleForm;

use Vankosoft\UsersBundle\Model\UserInfo;
use Vankosoft\UsersBundle\Model\UserActivity;
use Vankosoft\UsersBundle\Model\UserNotification;
use Vankosoft\UsersBundle\Model\ResetPasswordRequest;
use Vankosoft\UsersBundle\Repository\ResetPasswordRequestRepository;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder    = new TreeBuilder( 'vs_users' );
        $rootNode       = $treeBuilder->getRootNode();
        
        $rootNode
            ->children()
                ->scalarNode( 'driver' )->defaultValue( SyliusResourceBundle::DRIVER_DOCTRINE_ORM )->cannotBeEmpty()->end()
            ->end()
        ;
        $this->addResourcesSection( $rootNode );

        return $treeBuilder;
    }

    private function addResourcesSection( ArrayNodeDefinition $node ): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                    
                        // Begin Avatar Image
                        ->arrayNode( 'avatar_image' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( AvatarImage::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( AvatarImageRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        // End Avatar Image
                    
                        // Begin Users
                        ->arrayNode('users')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( User::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( UsersController::class )->cannotBeEmpty()->end()
                                        //->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( UsersRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( UserFormType::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        // End Users
                        
                        // Begin User Roles
                        ->arrayNode( 'user_roles' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( UserRole::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( UsersRolesController::class )->cannotBeEmpty()->end()
                                        //->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( UserRolesRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( UserRoleForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        // End User Roles
                        
                        // Begin UserInfo
                        ->arrayNode('user_info')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( UserInfo::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        // End UserInfo
                        
                        // Begin UserActivity
                        ->arrayNode('user_activity')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( UserActivity::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        // End UserActivity
                        
                        // Begin UserNotification
                        ->arrayNode('user_notification')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( UserNotification::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        // End UserNotification
                        
                        // Begin ResetPasswordRequest
                        ->arrayNode('reset_password_request')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( ResetPasswordRequest::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( ResetPasswordRequestRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        // End ResetPasswordRequest
                        
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
