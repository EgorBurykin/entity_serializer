<?php

namespace Jett\JSONEntitySerializerBundle\Tests\app;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Jett\JSONEntitySerializerBundle\JettJSONEntitySerializerBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * @return array
     */
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new JettJSONEntitySerializerBundle(),
        ];

        return $bundles;
    }

    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
    }
}
