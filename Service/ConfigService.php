<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Service;

/**
 * Class ConfigService - contains bundle config.
 */
class ConfigService
{
    private $_config;
    private $_hash;

    /**
     * Sets configuration.
     *
     * @param $config
     * @param mixed $hash
     */
    public function __construct($config, $hash)
    {
        $this->_config = $config;
        $this->_hash = $hash;
    }

    public function getVirtualAnnotation()
    {
        return $this->_config['virtual_annotation'];
    }

    public function getIgnoreAnnotation()
    {
        return $this->_config['ignore_annotation'];
    }

    public function getGetterAnnotation()
    {
        return $this->_config['getter_annotation'];
    }

    public function getNameAnnotation()
    {
        return $this->_config['name_annotation'];
    }

    public function getEntities()
    {
        return $this->_config['entities'];
    }

    public function getConfigHash()
    {
        return $this->_hash;
    }

    public static function getHash($config)
    {
        return md5(json_encode($config));
    }

    /**
     * Checks if serializer can serialize entity.
     *
     * @param $entity
     *
     * @return bool
     */
    public function canSerializeEntity($entity)
    {
        return isset($this->_config['entities'][$entity]);
    }
}
