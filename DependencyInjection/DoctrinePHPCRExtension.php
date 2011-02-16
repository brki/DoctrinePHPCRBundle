<?php

namespace Bundle\DoctrinePHPCRBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Resource\FileResource;

class DoctrinePHPCRExtension extends Extension
{
    /**
     * @param array $config An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = array();
        foreach ($configs as $conf) {
            $config = array_merge($config, $conf);
        }

        if (!array_key_exists('backend', $config)) {
            throw new \UnexpectedValueException('The DoctrinePHPCRBundle load entry expects a backend subnode');
        }

        $this->loadBackendDefaults($config['backend'], $container);
        $this->loadOdmDefaults($config, $container);
    }

    /**
     * Loads the Jackalope configuration.
     *
     * @param array $config An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function loadBackendDefaults(array $config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jackalope.repository')) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('jackalope.xml');
        }

        if (!isset($config['workspace'])) {
            throw new \Exception('Jackalope\'s workspace parameter is mandatory');
        }

        $options = array();
        foreach (array('url', 'user', 'pass', 'workspace', 'transport') as $var) {
            if (isset($config[$var])) {
                $options[$var] = $config[$var];
                $container->setParameter('jackalope.options.'.$var, $config[$var]);
            }
        }
    }

    /**
     * Loads the DoctrinePHPCR ODM configuration.
     *
     * @param array $config An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function loadOdmDefaults(array $config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine.phpcr.document_manager')) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('phpcr.xml');
        }

        $container->setParameter('doctrine.phpcr_odm.metadata_driver.mapping_dirs', $this->findBundleSubpaths('Resources/config/doctrine/metadata/odm', $container));
        $container->setParameter('doctrine.phpcr_odm.metadata_driver.document_dirs', $this->findBundleSubpaths('Document', $container));
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

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/doctrine-phpcr-odm';
    }

    public function getAlias()
    {
        return 'doctrine_phpcr';
    }
}
