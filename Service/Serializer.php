<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Service;

use Jett\JSONEntitySerializerBundle\Transformer\TransformerInterface;

/**
 * Class Serializer provides serialization of classic doctrine entities with
 * single-column key called `id` to JSON.
 */
class Serializer implements SerializerInterface
{
    /** @var BaseSerializer */
    protected $_instance;

    protected $_class;

    /**
     * Serializer constructor.
     *
     * @param SerializerBuilder $generator
     *
     * @throws \Jett\JSONEntitySerializerBundle\Exception\ClassNotFoundException
     * @throws \Jett\JSONEntitySerializerBundle\Exception\RenderFailedException
     * @throws \Jett\JSONEntitySerializerBundle\Exception\SampleObjectException
     */
    public function __construct(SerializerBuilder $generator)
    {
        $generator->generateService();
        $this->_instance = $generator->loadSerializer();
        $this->_class = $generator->getClassName();
    }

    /**
     * Adds transformers to pool.
     *
     * @param TransformerInterface $transformer
     */
    public function addTransformer(TransformerInterface $transformer)
    {
        $this->_instance->addTransformer($transformer);
    }

    /**
     * Normalizes doctrine entity with single-column key called id to plain object.
     *
     * plain object will contain only properties that could be found in the sample provided with
     * configuration or passed by value.
     *
     * @param $entity
     * @param object|string|null $sample
     *
     * @return object|object[]
     */
    public function toPureObject($entity, $sample = null)
    {
        return $this->_instance->toPureObject($entity, $sample);
    }

    /**
     * Serializes doctrine entity with single-column key called id to plain JSON.
     *
     * @param $entity
     * @param string|object|null $sample - defines what fields should be included to result
     *
     * @return string
     */
    public function serialize($entity, $sample = null)
    {
        return $this->_instance->serialize($entity, $sample);
    }

    /**
     * Cleans cache.
     */
    public function clearCache()
    {
        $this->_class::clearCache();
    }
}
