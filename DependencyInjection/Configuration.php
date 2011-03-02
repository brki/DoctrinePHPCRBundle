<?php

namespace Bundle\DoctrinePHPCRBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\DependencyInjection\Configuration\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('doctrine_phpcr', 'array');

        $rootNode
            ->arrayNode('backend')
                ->cannotBeEmpty()
                ->addDefaultsIfNotSet()
                    ->scalarNode('workspace')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('url')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('user')->defaultNull()->end()
                    ->scalarNode('pass')->defaultNull()->end()
                    ->scalarNode('transport')->defaultNull()->end()
                ->end();

        return $treeBuilder->buildTree();
    }

}
