<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('jett_json_entity_serializer');
        $rootNode
            ->children()
                ->scalarNode('name_annotation')
                    ->defaultValue('Jett\\JSONEntitySerializerBundle\\Annotation\\SerializedName')
                ->end()
                ->scalarNode('ignore_annotation')
                    ->defaultValue('Jett\\JSONEntitySerializerBundle\\Annotation\\Ignore')
                ->end()
                ->scalarNode('virtual_annotation')
                    ->defaultValue('Jett\\JSONEntitySerializerBundle\\Annotation\\VirtualProperty')
                ->end()
                ->scalarNode('getter_annotation')
                    ->defaultValue('Jett\\JSONEntitySerializerBundle\\Annotation\\GetterName')
                ->end()
                ->arrayNode('entities')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->arrayNode('samples')->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
