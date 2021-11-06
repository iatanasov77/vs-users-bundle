<?php namespace VS\UsersBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use VS\UsersBundle\Model\User;
use VS\UsersBundle\Model\UserInterface;
use VS\UsersBundle\Controller\UsersController;
use VS\UsersBundle\Repository\UsersRepository;
use VS\UsersBundle\Form\UserFormType;

use VS\UsersBundle\Model\UserRole;
use VS\UsersBundle\Model\UserRoleInterface;
use VS\UsersBundle\Controller\UsersRolesController;
use VS\UsersBundle\Form\UserRoleForm;

use VS\UsersBundle\Model\UserInfo;
use VS\UsersBundle\Model\UserInfoInterface;
use VS\UsersBundle\Model\UserActivity;
use VS\UsersBundle\Model\UserActivityInterface;
use VS\UsersBundle\Model\UserNotification;
use VS\UsersBundle\Model\UserNotificationInterface;
use VS\UsersBundle\Model\ResetPasswordRequest;
use VS\UsersBundle\Repository\ResetPasswordRequestRepository;

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
    public function getConfigTreeBuilder()
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
                    
                        // Begin Users
                        ->arrayNode('users')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( User::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( UserInterface::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( UserRoleInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( UsersRolesController::class )->cannotBeEmpty()->end()
                                        //->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( UserInfoInterface::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( UserActivityInterface::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( UserNotificationInterface::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( ResourceInterface::class )->cannotBeEmpty()->end()
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
