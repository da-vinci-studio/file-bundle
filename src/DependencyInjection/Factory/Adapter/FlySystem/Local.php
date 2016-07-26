<?php

namespace Dvs\FileBundle\DependencyInjection\Factory\Adapter\FlySystem;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Dvs\FileBundle\DependencyInjection\Factory\FactoryInterface;

class Local implements FactoryInterface
{
    public function getKey()
    {
        return 'flysystem_local';
    }

    public function create(ContainerBuilder $container, $id, array $config, $name)
    {
        $container
            ->setDefinition('oneup_flysystem.adapter.local', new DefinitionDecorator('oneup_flysystem.adapter.local.abstract'))
            ->replaceArgument(0, $config['directory'])
            ->replaceArgument(1, $config['writeFlags'])
            ->replaceArgument(2, $config['linkHandling'])
        ;

        $container
            ->setDefinition('oneup_flysystem.filesystem.local', new DefinitionDecorator('oneup_flysystem.filesystem.abstract'))
            ->replaceArgument(0, new Reference('oneup_flysystem.adapter.local'))
            ->replaceArgument(1, $config)
        ;

        $container
            ->setDefinition($id, new DefinitionDecorator('dvs.file.adapter.fly_system.local'))
            ->replaceArgument(0, new Reference('oneup_flysystem.filesystem.local'))
        ;
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}
