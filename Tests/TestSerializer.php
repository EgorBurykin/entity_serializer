<?php

namespace Jett\JSONEntitySerializerBundle\Tests;

use Jett\JSONEntitySerializerBundle\Service\SerializerInterface;

class TestSerializer implements SerializerInterface
{
    public function toPureObject($entity, $sample = null)
    {
        return $entity;
    }

    public function serialize($entity, $sample = null)
    {
        return json_encode($entity);
    }
}
