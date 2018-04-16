<?php

namespace Jett\JSONEntitySerializerBundle\Tests\Service\Serializer;

use Doctrine\ORM\EntityManager;
use Jett\JSONEntitySerializerBundle\Service\Serializer;
use Jett\JSONEntitySerializerBundle\Service\SerializerInterface;
use Jett\JSONEntitySerializerBundle\Tests\app\AppKernel;
use Jett\JSONEntitySerializerBundle\Tests\Consts;
use Jett\JSONEntitySerializerBundle\Tests\Entity\Programmer;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityFive;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityFour;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityOne;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntitySix;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityThree;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityTwo;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeneralCasesTest extends SerializerTestCase
{

    public function testAccessibleByPublicName() {
        $this->serializer = self::$kernel->getContainer()->get('entity_serializer');
    }

    public function testClearCache() {
        try {
            $this->serializer->cleanCache();
        } catch (\Exception $exception) {
            $this->fail($exception->getMessage());
        }

    }

    public function testSimpleObjectSerialization()
    {
        $obj = $this->serializer->toPureObject(EntityOne::get());
        $this->assertEquals(Consts::ID, $obj->id);
        $this->assertEquals(Consts::TITLE, $obj->title);
    }

    public function testObjectWithDatetimeSerialization()
    {
        $obj = $this->serializer->toPureObject(EntityTwo::get());
        $this->assertEquals(Consts::ID, $obj->id);
        $this->assertEquals(Consts::TITLE, $obj->title);
        $this->assertEquals(Consts::DATE, $obj->date);
        $this->assertEquals(Consts::DATE, $obj->datetime);
    }

    public function testObjectWithObjectSerialization()
    {
        $obj = $this->serializer->serialize(EntityThree::get());
        $json = '{
            "object": {"prop1":1,"prop2":"title"},
            "datetime": "2017-01-01T00:00:00+00:00",
            "date":"2017-01-01T00:00:00+00:00", "id":1, "title":"title"
        }';
        $this->assertJsonStringEqualsJsonString($json, $obj);
    }

    public function testObjectWithArraySerialization()
    {
        $obj = $this->serializer->toPureObject(EntityFour::get());
        $this->assertEquals(Consts::ID, $obj->id);
        $this->assertEquals(Consts::TITLE, $obj->title);
        $this->assertEquals(Consts::DATE, $obj->date);
        $this->assertEquals(Consts::DATE, $obj->datetime);
        for ($i = 1; $i <= 3; ++$i) {
            $this->assertEquals(Consts::ID, $obj->{'array'.$i}['id']);
            $this->assertEquals(Consts::TITLE, $obj->{'array'.$i}['title']);
        }
    }

    public function testObjectWithManyToOneRelationSerialization()
    {
        $obj = $this->serializer->serialize(EntityFive::get());
        $this->assertJsonStringEqualsJsonString('{"id":1,"title":"title","entity":1}', $obj);
        $obj = $this->serializer->serialize(EntityFive::get(), 'extended');
        $this->assertJsonStringEqualsJsonString(
            '{"id":1,"title":"title","entity":{"id":1,"title":"title"}}',
            $obj
        );
    }

    public function testObjectWithOneToManyRelationSerialization()
    {
        $obj = $this->serializer->toPureObject(EntitySix::get());
        $this->assertEquals(Consts::ID, $obj->id);
        $this->assertEquals(Consts::TITLE, $obj->title);
        for ($i = 1; $i <= 2; ++$i) {
            $this->assertEquals(2, count($obj->{'entities'.$i}));
            $this->assertTrue(is_array($obj->{'entities'.$i}));
            $this->assertEquals(Consts::ID, $obj->{'entities'.$i}[0]);
        }
    }
}
