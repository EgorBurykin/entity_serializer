<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Info;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Jett\JSONEntitySerializerBundle\Annotation\AnnotationHelper;
use Jett\JSONEntitySerializerBundle\Service\ConfigService;

/**
 * Class InfoProvider responsible for getting an information for generator.
 */
class InfoProvider
{
    protected $_em;

    private $_configService;

    private $_annotationHelper;

    public function __construct(
        AnnotationHelper $annotationHelper,
        ConfigService $configService,
        EntityManagerInterface $em
    ) {
        $this->_em = $em;
        $this->_configService = $configService;
        $this->_annotationHelper = $annotationHelper;
    }

    /**
     * Returns class info.
     *
     * @param string $className
     *
     * @throws \ReflectionException if class is absent
     *
     * @return array
     */
    public function getInfoForClass(string $className): array
    {
        $reflection = new \ReflectionClass($className);
        $properties = $reflection->getProperties();
        /** @var FieldInfo[] $fields */
        $fields = [];
        /** @var RelationInfo[] $links */
        $links = [];

        $put = function ($node) use (&$fields, &$links) {
            if (!$node) {
                return;
            }

            if ($node instanceof RelationInfo) {
                $links[] = $node;
            } else {
                $fields[] = $node;
            }
        };

        $classMeta = $this->_em->getClassMetadata($className);

        foreach ($properties as $prop) {
            if ($this->_annotationHelper->isIgnored($prop)) {
                continue;
            }

            $put($this->createInfoFromProperty($prop, $classMeta));
        }

        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (!($name = $this->_annotationHelper->getVirtualPropertyName($method))) {
                continue;
            }

            $put($this->createInfoFromVirtualProperty($name, $method));
        }

        return [$fields, $links];
    }

    /**
     * Creates node based on virtual property of the entity.
     *
     * @param string            $propName
     * @param \ReflectionMethod $method
     *
     * @return GeneratorInfo
     */
    public function createInfoFromVirtualProperty(string $propName, \ReflectionMethod $method): GeneratorInfo
    {
        if (!$info = $this->_annotationHelper->getTypeInfo($method)) {
            return new FieldInfo($propName, '', $method->name);
        } elseif ('link' !== $info->type) {
            return new FieldInfo($propName, $info->type, $method->name);
        } elseif ('link' === $info->type) {
            if (!$this->_configService->canSerializeEntity($info->targetEntity)) {
                return null;
            }

            $relation = new RelationInfo($propName, $method->name);
            $relation->entity = $info->targetEntity;
            $relation->isSingleValued = $info->singleValued;

            return $relation;
        }

        return null;
    }

    /**
     * Creates node for embedded object.
     *
     * @param \ReflectionProperty $prop
     * @param ClassMetadata       $classMeta
     *
     * @throws \ReflectionException
     *
     * @return FieldInfo
     */
    protected function createInfoForEmbedded(\ReflectionProperty $prop, ClassMetadata $classMeta): FieldInfo
    {
        $field = $this->createInfoForSimpleField($prop, 'embed');
        $embeddings = $classMeta->embeddedClasses;
        list($field->fields, $field->relations) = $this->getInfoForClass($embeddings[$prop->name]['class']);

        return $field;
    }

    /**
     * Creates an info about relation.
     *
     * @param \ReflectionProperty $prop
     * @param ClassMetadata       $classMeta
     *
     * @return RelationInfo|null
     */
    protected function createInfoForRelation(\ReflectionProperty $prop, ClassMetadata $classMeta)
    {
        $mappings = $classMeta->associationMappings;
        $entity = $mappings[$prop->name]['targetEntity'];

        if (!$this->_configService->canSerializeEntity($entity)) {
            return null;
        }

        $relation = new RelationInfo();
        $relation->entity = $entity;
        $relation->isSingleValued = $classMeta->isSingleValuedAssociation($prop->name);
        $relation->name = $this->_annotationHelper->getSerializedName($prop);
        $relation->getter = $this->_annotationHelper->getRealGetterName($prop, true, $relation->isSingleValued);

        return $relation;
    }

    /**
     * Creates an info from simple field.
     *
     * @param \ReflectionProperty $prop - reflection of particular field
     * @param string              $type - ORM type for this field
     *
     * @return FieldInfo
     */
    protected function createInfoForSimpleField(\ReflectionProperty $prop, string $type): FieldInfo
    {
        $field = new FieldInfo();
        $field->type = $type;
        $field->getter = $this->_annotationHelper->getRealGetterName($prop, false, true);
        $field->name = $this->_annotationHelper->getSerializedName($prop);

        return $field;
    }

    /**
     * Creates an info based on a property of the entity.
     *
     * @param \ReflectionProperty $prop
     * @param ClassMetadata       $classMeta
     *
     * @throws \ReflectionException
     *
     * @return FieldInfo|RelationInfo|null
     */
    protected function createInfoFromProperty(\ReflectionProperty $prop, ClassMetadata $classMeta)
    {
        $embeddings = $classMeta->embeddedClasses;

        if (isset($embeddings[$prop->name])) {
            return $this->createInfoForEmbedded($prop, $classMeta);
        }

        $mappings = $classMeta->associationMappings;

        if (isset($mappings[$prop->name])) {
            return $this->createInfoForRelation($prop, $classMeta);
        }

        $fields = $classMeta->fieldMappings;

        return $this->createInfoForSimpleField($prop, $fields[$prop->name]['type'] ?? '');
    }
}
