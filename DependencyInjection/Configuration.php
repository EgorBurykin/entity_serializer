<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\DependencyInjection;

use Jett\JSONEntitySerializerBundle\Annotation\GetterName;
use Jett\JSONEntitySerializerBundle\Annotation\Ignore;
use Jett\JSONEntitySerializerBundle\Annotation\SerializedName;
use Jett\JSONEntitySerializerBundle\Annotation\VirtualProperty;
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
                    ->defaultValue(SerializedName::class)
                ->end()
                ->scalarNode('ignore_annotation')
                    ->defaultValue(Ignore::class)
                ->end()
                ->scalarNode('virtual_annotation')
                    ->defaultValue(VirtualProperty::class)
                ->end()
                ->scalarNode('getter_annotation')
                    ->defaultValue(GetterName::class)
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
