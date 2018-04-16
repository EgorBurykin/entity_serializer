<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Tests\Transformers;

use Jett\JSONEntitySerializerBundle\Tests\Consts;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntityOne;
use Jett\JSONEntitySerializerBundle\Tests\Entity\EntitySix;
use Jett\JSONEntitySerializerBundle\Tests\SerializerTestCase;
use Jett\JSONEntitySerializerBundle\Transformer\CallbackTransformer;
use Jett\JSONEntitySerializerBundle\Transformer\Common\DateTimeTransformer;
use Jett\JSONEntitySerializerBundle\Transformer\Common\LowerTransformer;
use Jett\JSONEntitySerializerBundle\Transformer\Common\UpperTransformer;

class TransformerTest extends SerializerTestCase
{
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

    public function testOnSimpleFields()
    {
        $sample = (object) ['id' => '', 'title' => 'upper'];
        $entity = EntityOne::get();
        $object = $this->serializer->toPureObject($entity, $sample);
        $this->assertNotEmpty($object->title);
        $this->assertEquals(strtoupper(Consts::TITLE), $object->title);
    }

    public function testOnEntity()
    {
        $entity = EntitySix::get();
        $sample = (object) ['id' => '', 'title' => '', 'entities1' => 'id', 'entities2' => 'title'];
        $object = $this->serializer->serialize($entity, $sample);
        $this->assertJsonStringEqualsJsonString('{"id":1,"title":"title","entities1":[1,1],"entities2":["title","title"]}', $object);
    }

    public function testCallbackTransformer()
    {
        $call = function ($val) {
            return substr($val, 0, 1);
        };
        $tr = new CallbackTransformer($call, 'firstChar');
        $this->serializer->addTransformer($tr);
        $sample = (object) ['id' => '', 'title' => 'firstChar'];
        $entity = EntityOne::get();
        $object = $this->serializer->toPureObject($entity, $sample);
        $this->assertNotEmpty($object->title);
        $this->assertEquals(substr(Consts::TITLE, 0, 1), $object->title);
    }
}
