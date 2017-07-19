<?php

namespace Fluedis\RedmineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
        $rootNode = $treeBuilder->root('fluedis_redmine');

        $rootNode
            ->children()
                ->scalarNode('enabled')->defaultTrue()->end()
                ->scalarNode('uri')->isRequired()->end()
                ->scalarNode('api_key')->isRequired()->end()
                ->scalarNode('tracker_id')->defaultValue(1)->end()
                ->scalarNode('project_id')->isRequired()->end()
                ->scalarNode('priority_id')->defaultValue(3)->end()
                ->scalarNode('status_id')->defaultNull()->end()
                ->scalarNode('assigned_to')->defaultNull()->end()
                ->arrayNode('watchers')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
