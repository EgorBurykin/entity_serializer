<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Annotation;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\Inflector;
use Jett\JSONEntitySerializerBundle\Service\ConfigService;

class AnnotationHelper
{
    private $_reader;
    private $_getterAnnotation;
    private $_serializedNameAnnotation;
    private $_ignoreAnnotation;
    private $_virtualPropertyAnnotation;

    public function __construct(
        Reader $reader, ConfigService $configService
    ) {
        $this->_reader = $reader;
        $this->_getterAnnotation = $configService->getGetterAnnotation();
        $this->_serializedNameAnnotation = $configService->getNameAnnotation();
        $this->_ignoreAnnotation = $configService->getIgnoreAnnotation();
        $this->_virtualPropertyAnnotation = $configService->getVirtualAnnotation();
    }

    /**
     * Returns a real getter name for a property.
     *
     * @param \ReflectionProperty $prop
     * @param bool                $isLink
     * @param bool                $isSingle
     *
     * @return string
     */
    public function getRealGetterName(\ReflectionProperty $prop, bool $isLink, bool $isSingle): string
    {
        $annotation = $this->_reader->getPropertyAnnotation($prop, $this->_getterAnnotation);

        if ($annotation && $annotation->value) {
            return $annotation->value;
        }

        $propName = $prop->name;

        if ($isLink && !$isSingle) {
            return 'get'.ucfirst(Inflector::pluralize($propName));
        }

        return 'get'.ucfirst($propName);
    }

    /**
     * Returns the property name for serialized presentation.
     *
     * @param \ReflectionProperty $prop
     *
     * @return string
     */
    public function getSerializedName(\ReflectionProperty $prop): string
    {
        $annotation = $this->_reader->getPropertyAnnotation($prop, $this->_serializedNameAnnotation);

        return $annotation ? $annotation->name : $prop->name;
    }

    /**
     * Returns if the property should be ignored.
     *
     * @param \ReflectionProperty $prop
     *
     * @return bool
     */
    public function isIgnored(\ReflectionProperty $prop): bool
    {
        $annotation = $this->_reader->getPropertyAnnotation($prop, $this->_ignoreAnnotation);

        return (bool) $annotation;
    }

    public function getTypeInfo(\ReflectionMethod $method)
    {
        return $this->_reader->getMethodAnnotation($method, TypeInfo::class);
    }

    /**
     * Returns the virtual property name.
     *
     * @param \ReflectionMethod $method
     *
     * @return string|bool
     */
    public function getVirtualPropertyName(\ReflectionMethod $method)
    {
        $annotation = $this->_reader->getMethodAnnotation($method, $this->_virtualPropertyAnnotation);

        return $annotation->name ?? null;
    }
}