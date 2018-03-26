<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\DependencyInjection\Compile;

use Jett\JSONEntitySerializerBundle\Service\Serializer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TransformersCompilerPass implements CompilerPassInterface
{

    const TAG = 'entity_serializer.transformer';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(Serializer::class)) {
            return;
        }
        $builder = $container->getDefinition(Serializer::class);
        $services = $container->findTaggedServiceIds(self::TAG);
        foreach($services as $id => $tags) {
            $builder->addMethodCall('addTransformer', [$container->getDefinition($id)]);
        }
    }

}