<?php

namespace Dvs\FileBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class DvsFileExtension extends Extension
{
    /**
     * File systems.
     */
    const FILE_SYSTEM_FLY_SYSTEM = 'flysystem';

    /**
     * @var \Dvs\FileBundle\Interfaces\FileSystem
     */
    private $adapterFactories;

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $adapterFactories = $this->getFactories();

        $configuration = new Configuration($adapterFactories);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('adapters.xml');
        $loader->load('services.xml');

        foreach ($config['filesystems'] as $name => $filesystem) {
            $this->create($name, $filesystem, $container, $adapterFactories);
        }
    }

    /**
     * @param $name
     * @param array            $config
     * @param ContainerBuilder $container
     * @param array            $factories
     *
     * @return string
     */
    private function create($name, array $config, ContainerBuilder $container, array $factories)
    {
        foreach ($config['adapter'] as $key => $adapter) {
            $adapterName = $key;
            $adapterFileSystem = sprintf(self::FILE_SYSTEM_FLY_SYSTEM.'_%s', $key);

            if (array_key_exists($adapterFileSystem, $factories)) {
                $adapterName = $adapterFileSystem;
            }

            if (array_key_exists($adapterName, $factories)) {
                $id = sprintf('dvs.file.%s.filesystem', $name);
                $factories[$adapterName]->create($container, $id, $adapter, $adapterName);

                return $id;
            }
        }

        throw new \LogicException(sprintf('The adapter \'%s\' is not configured.', $name));
    }

    /**
     * @return array
     */
    private function getFactories()
    {
        $container = new ContainerBuilder();

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('factories.xml');

        return $this->getAdapterFactories($container);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getAdapterFactories(ContainerBuilder $container)
    {
        if (null !== $this->adapterFactories) {
            return $this->adapterFactories;
        }

        $factories = array();
        $services = $container->findTaggedServiceIds('dvs.file.factory.adapter.adapter_local_factory');

        foreach (array_keys($services) as $id) {
            $factory = $container->get($id);
            $factories[str_replace('-', '_', $factory->getKey())] = $factory;
        }

        return $this->adapterFactories = $factories;
    }
}
