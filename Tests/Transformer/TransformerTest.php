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
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityOne;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntitySix;
use Jett\JSONEntitySerializerBundle\Transformer\CallbackTransformer;
use Jett\JSONEntitySerializerBundle\Transformer\Common\DateTimeTransformer;
use Jett\JSONEntitySerializerBundle\Transformer\Common\LowerTransformer;
use Jett\JSONEntitySerializerBundle\Transformer\Common\UpperTransformer;
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

    public function testDateTransformer()
    {
        $transformer = new DateTimeTransformer();
        $date = \DateTime::createFromFormat(DATE_W3C, Consts::DATE);
        $this->assertEquals(Consts::DATE, $transformer->transform($date));
    }

    public function testUpperTransformer()
    {
        $transformer = new UpperTransformer();
        $this->assertEquals(strtoupper(Consts::TITLE), $transformer->transform(Consts::TITLE));
    }

    public function testLowerTransformer()
    {
        $tr = new LowerTransformer();
        $this->assertEquals(strtolower(Consts::TITLE), $tr->transform(Consts::TITLE));
    }

    //TODO: write entity transformer cases

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
        $sample = (object)['id'=>'','title'=>'', 'entities1'=>'id', 'entities2'=>'title'];
        $object = $this->serializer->toPureObject($entity, $sample);
        $this->assertContains(Consts::ID, $object->entities1);
        $this->assertContains(Consts::TITLE, $object->entities2);
    }
}