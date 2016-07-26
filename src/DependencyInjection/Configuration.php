<?php

namespace Dvs\FileBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use League\Flysystem\Adapter\Local;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dvs_file');

        $this->addFileSystemSection($rootNode);
        $rootNode
            ->children()
            ->end()
        ;

        return $treeBuilder;
    }

    private function addFileSystemSection(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('filesystem')
            ->children()
                ->arrayNode('filesystems')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('adapter')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('directory')->isRequired()->end()
                                        ->scalarNode('writeFlags')->defaultValue(LOCK_EX)->end()
                                        ->scalarNode('linkHandling')->defaultValue(Local::DISALLOW_LINKS)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
