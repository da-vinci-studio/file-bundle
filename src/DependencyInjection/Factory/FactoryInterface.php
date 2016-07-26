<?php

namespace Dvs\FileBundle\DependencyInjection\Factory;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface FactoryInterface
{
    public function getKey();
    public function create(ContainerBuilder $container, $id, array $config, $name);
    public function addConfiguration(NodeDefinition $builder);
}
