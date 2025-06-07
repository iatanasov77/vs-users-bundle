<?php namespace Vankosoft\UsersBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VSUsersExtension extends AbstractResourceExtension
{
    /**
     * {@inheritDoc}
     */
    public function load( array $config, ContainerBuilder $container ): void
    {
        $config = $this->processConfiguration( $this->getConfiguration([], $container), $config );
        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        //var_dump($config); die;
        $this->registerResources( 'vs_users', $config['driver'], $config['resources'], $container );
        $loader->load( 'services.yaml' );
    }
}
