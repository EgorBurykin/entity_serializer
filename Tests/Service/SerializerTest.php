<?php

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
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SerializerTest extends KernelTestCase
{
    /** @var SerializerInterface */
    private $serializer;
    /** @var EntityManager */
    private $em;

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();
        $this->serializer = self::$kernel->getContainer()->get(Serializer::class);

        $this->em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testAccessibleByPublicName() {
        $this->serializer = self::$kernel->getContainer()->get('entity_serializer');
    }

    public function testSimpleObjectSerialization()
    {
        $obj = $this->serializer->toPureObject(EntityOne::get());
        $this->assert1($obj);
    }

    public function testObjectWithDatetimeSerialization()
    {
        $obj = $this->serializer->toPureObject(EntityTwo::get());
        $this->assert1($obj);
        $this->assert2($obj);
    }

    public function testObjectWithObjectSerialization()
    {

        $obj = $this->serializer->toPureObject(EntityThree::get());
        $this->assert1($obj);
        $this->assert2($obj);
        $this->assert3($obj);
    }

    public function testObjectWithArraySerialization()
    {
        $obj = $this->serializer->toPureObject(EntityFour::get());
        $this->assert1($obj);
        $this->assert2($obj);
        $this->assert4($obj);
    }

    public function testObjectWithManyToOneRelationSerialization()
    {
        $obj = $this->serializer->toPureObject(EntityFive::get());
        $this->assert1($obj);
        $this->assert5($obj);
    }

    public function testObjectWithOneToManyRelationSerialization()
    {
        $obj = $this->serializer->toPureObject(EntitySix::get());
        $this->assert1($obj);
        $this->assert6($obj);
    }

    protected static function getKernelClass()
    {
        return AppKernel::class;
    }

    protected function assert1($obj)
    {
        $this->assertEquals(Consts::ID, $obj->id);
        $this->assertEquals(Consts::TITLE, $obj->title);
    }

    protected function assert2($obj)
    {
        $this->assertEquals(Consts::DATE, $obj->date);
        $this->assertEquals(Consts::DATE, $obj->datetime);
    }

    protected function assert3($obj)
    {
        $this->assertEquals(Consts::ID, $obj->object->id);
        $this->assertEquals(Consts::TITLE, $obj->object->title);
    }

    protected function assert4($obj)
    {
        for ($i = 1; $i <= 3; ++$i) {
            $this->assertEquals(Consts::ID, $obj->{'array'.$i}['id']);
            $this->assertEquals(Consts::TITLE, $obj->{'array'.$i}['title']);
        }
    }

    protected function assert5($obj)
    {
        $this->assertEquals(Consts::ID, $obj->entity);
    }

    protected function assert6($obj)
    {
        for ($i = 1; $i <= 2; ++$i) {
            $this->assertEquals(2, count($obj->{'entities'.$i}));
            $this->assertTrue(is_array($obj->{'entities'.$i}));
            $this->assertEquals(Consts::ID, $obj->{'entities'.$i}[0]);
        }
    }
}
