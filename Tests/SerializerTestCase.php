<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Tests;


use Doctrine\ORM\EntityManager;
use Jett\JSONEntitySerializerBundle\Service\Serializer;
use Jett\JSONEntitySerializerBundle\Service\SerializerInterface;
use Jett\JSONEntitySerializerBundle\Tests\app\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SerializerTestCase extends KernelTestCase
{
    /** @var SerializerInterface */
    protected $serializer;
    /** @var EntityManager */
    protected $em;

    protected static function getKernelClass()
    {
        return AppKernel::class;
    }

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();
        $this->serializer = self::$kernel->getContainer()->get(Serializer::class);

        $this->em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

}
