<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Tests;

namespace Jett\JSONEntitySerializerBundle\Tests;

use Doctrine\ORM\EntityManager;
use Jett\JSONEntitySerializerBundle\Service\Serializer;
use Jett\JSONEntitySerializerBundle\Service\SerializerInterface;
use Jett\JSONEntitySerializerBundle\Tests\app\AppKernel;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityFive;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityFour;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityOne;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntitySix;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityThree;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityTwo;
use Jett\JSONEntitySerializerBundle\Transformer\CallbackTransformer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class TransformerTest extends KernelTestCase
{
    /** @var SerializerInterface */
    private $serializer;
    /** @var EntityManager */
    private $em;

    protected function setUp()
    {
        parent::setUp();
        $kernel = static::bootKernel();
        $this->serializer = $kernel->getContainer()->get(Serializer::class);
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testOnSimpleFields() {
        $call = function($data) {
            return strtoupper($data);
        };
        $this->serializer->addTransformer(new CallbackTransformer($call,'upper', 'string'));
        $sample = (object)['id'=>'','title'=>'upper'];
        $entity = EntityOne::get();
        $object = $this->serializer->toPureObject($entity, $sample);
        $this->assertNotEmpty($object->title);
        $this->assertEquals(strtoupper(Consts::TITLE), $object->title);
    }

    public function testOnEntity() {
        $entity = EntitySix::get();
        $titleTransformer = function($data) {
            return $data->getTitle();
        };
        $idTransformer = function($data) {
            return $data->getId();
        };
        $this->serializer->addTransformer(new CallbackTransformer($titleTransformer, 'title', [EntityOne::class, EntitySix::class]));
        $this->serializer->addTransformer(new CallbackTransformer($idTransformer, 'id', [EntityOne::class, EntitySix::class]));
        $sample = (object)['id'=>'','title'=>'', 'entities1'=>'id', 'entities2'=>'title'];
        $object = $this->serializer->toPureObject($entity, $sample);
        $this->assertContains(Consts::ID, $object->entities1);
        $this->assertContains(Consts::TITLE, $object->entities2);
    }
}