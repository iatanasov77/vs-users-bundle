<?php namespace Vankosoft\UsersBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VSUsersExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration( $this->getConfiguration([], $container), $config );
        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        //var_dump($config); die;
        $this->registerResources( 'vs_users', $config['driver'], $config['resources'], $container );
        $loader->load( 'services.yml' );
    }
    
    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');
        if(!isset($bundles['HearsayRequireJSBundle'])) {
            return;
        }
        
        if (!isset($bundles['IAAngularApplicationBundle'])) {
            throw new Exception('IAAngularAdminPanelBundle require IAAngularApplicationBundle');
        }
        
        /*
         * RequireJs Config
         */
        $requirejsConfig = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/requirejs.yml'));  
        $container->prependExtensionConfig('hearsay_require_js', $requirejsConfig);
        
        
        /*
         * Angular Config
         */
        $angularConfig = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/routing_angular.yml'));
        $container->prependExtensionConfig( 'ia_angular_application', $angularConfig);
    }
}
