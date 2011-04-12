<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Definition;

class DoctrinePHPCRExtension extends Extension
{
    /**
     * @param array $config An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->process($configuration->getConfigTree(), $configs);

        $this->loadBackendDefaults($config['backend'], $container);
        $this->loadOdmDefaults($config, $container);
        $this->createEventManagerDefinition($container);
    }

    /**
     * Loads the Jackalope configuration.
     *
     * @param array $config An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function loadBackendDefaults(array $config, ContainerBuilder $container)
    {
        $options = array();
        foreach (array('url', 'user', 'pass', 'workspace', 'transport') as $var) {
            $options[$var] = $config[$var];
            $container->setParameter('jackalope.options.'.$var, $config[$var]);
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('jackalope.xml');
    }

    /**
     * Loads the DoctrinePHPCR ODM configuration.
     *
     * @param array $config An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function loadOdmDefaults(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('phpcr.xml');

        $container->setParameter('doctrine.phpcr_odm.metadata_driver.mapping_dirs', $this->findBundleSubpaths('Resources/config/doctrine/metadata/odm', $container));
        $container->setParameter('doctrine.phpcr_odm.metadata_driver.document_dirs', $this->findBundleSubpaths('Document', $container));
    }

    /**
     * Create the default event manager definition.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function createEventManagerDefinition($container)
    {
        // for now, there is only one connection, so there will only be a 'default' event manager.
        $eventManagerId = 'doctrine.phpcr_odm.default_event_manager';
        if (!$container->hasDefinition($eventManagerId)) {
            $eventManagerDef = new Definition('%doctrine.phpcr_odm.event_manager_class%');
            $eventManagerDef->addTag('doctrine.phpcr_odm.event_manager');
            $eventManagerDef->setPublic(true);
            $container->setDefinition($eventManagerId, $eventManagerDef);
        }
    }

    /**
     * Finds existing bundle subpaths.
     *
     * @param string $path A subpath to check for
     * @param ContainerBuilder $container A ContainerBuilder configuration
     *
     * @return array An array of absolute directory paths
     */
    protected function findBundleSubpaths($path, ContainerBuilder $container)
    {
        $dirs = array();
        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            if (is_dir($dir = dirname($reflection->getFilename()).'/'.$path)) {
                $dirs[] = $dir;
                $container->addResource(new FileResource($dir));
            } else {
                // add the closest existing parent directory as a file resource
                do {
                    $dir = dirname($dir);
                } while (!is_dir($dir));
                $container->addResource(new FileResource($dir));
            }
        }
        return $dirs;
    }

    public function getAlias()
    {
        return 'doctrine_phpcr';
    }
}
