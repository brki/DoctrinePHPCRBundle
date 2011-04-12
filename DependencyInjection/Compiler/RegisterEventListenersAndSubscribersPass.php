<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterEventListenersAndSubscribersPass implements CompilerPassInterface
{
    protected $container;

    /**
     * Process.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        // For the moment there is only the default_event_manager, but this is written
        // anticipating that there will someday be support for multiple connections,
        // with per-connection event_managers.
        foreach ($container->findTaggedServiceIds('doctrine.phpcr_odm.event_manager') as $id => $tag) {
            $definition = $container->getDefinition($id);
            $prefix = substr($id, 0, -1 * strlen('_event_manager'));
            $this->registerListeners($prefix, $definition);
            $this->registerSubscribers($prefix, $definition);
        }
    }

    /**
     * Register services as event subscribers.
     *
     * @param string $prefix string like doctrine.phpcr_odm.my_connection_event_manager
     * @param Symfony\Component\DependencyInjection\Definition $definition
     */
    protected function registerSubscribers($prefix, $definition)
    {
        $subscribers = array_merge(
            $this->container->findTaggedServiceIds('doctrine.common.event_subscriber'),
            $this->container->findTaggedServiceIds($prefix.'_event_subscriber')
        );

        foreach ($subscribers as $id => $instances) {
            $definition->addMethodCall('addEventSubscriber', array(new Reference($id)));
        }
    }

    /**
     * Register event listeners.
     *
     * @param string $prefix string like doctrine.phpcr_odm.my_connection_event_manager
     * @param Symfony\Component\DependencyInjection\Definition $definition
     */
    protected function registerListeners($prefix, $definition)
    {
        $listeners = array_merge(
            $this->container->findTaggedServiceIds('doctrine.common.event_listener'),
            $this->container->findTaggedServiceIds($prefix.'_event_listener')
        );

        foreach ($listeners as $listenerId => $instances) {
            $events = array();
            foreach ($instances as $attributes) {
                if (isset($attributes['event'])) {
                    $events[] = $attributes['event'];
                }
            }

            if (0 < count($events)) {
                $definition->addMethodCall('addEventListener', array(
                    $events,
                    new Reference($listenerId),
                ));
            }
        }
    }
}
