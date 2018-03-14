<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Service;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Jett\JSONEntitySerializerBundle\Annotation\TypeInfo;
use Jett\JSONEntitySerializerBundle\Exception\ClassNotFoundException;
use Jett\JSONEntitySerializerBundle\Exception\RenderFailedException;
use Jett\JSONEntitySerializerBundle\Exception\SampleObjectException;
use Jett\JSONEntitySerializerBundle\Nodes\FieldNode;
use Jett\JSONEntitySerializerBundle\Nodes\GeneratorNode;
use Jett\JSONEntitySerializerBundle\Nodes\RelationNode;
use Symfony\Component\Filesystem\Filesystem;

class ClassGenerator
{
    protected $_reader;

    protected $_fs;

    protected $_em;

    private $_configService;

    private $_cachePath;

    private $_environment;

    public function __construct(
        Reader $reader,
        ConfigService $configService,
        EntityManager $em,
        $cachePath,
        $environment
    ) {
        $this->_reader = $reader;
        $this->_fs = new Filesystem();
        $this->_em = $em;
        $this->_configService = $configService;
        $this->_cachePath = $cachePath;
        $this->_environment = strtolower($environment);
    }

    public function resolveTargetEntity($entity, $className)
    {
        if (false === strpos($entity, '\\')) {
            $parts = explode('\\', $className);
            array_pop($parts);

            return implode('\\', $parts).'\\'.$entity;
        }
        $meta = $this->_em->getClassMetadata($entity);

        return $meta->name;
    }

    /**
     * Creates node based on virtual property of the entity.
     *
     * @param string            $propName
     * @param \ReflectionMethod $method
     *
     * @return GeneratorNode
     */
    public function handleVirtualProperty(string $propName, \ReflectionMethod $method): GeneratorNode
    {
        $info = $this->_reader->getMethodAnnotation($method, TypeInfo::class);

        if (!$info) {
            return $this->createVirtualFieldNode($propName, '', $method->name);
        } elseif ('link' !== $info->type) {
            return $this->createVirtualFieldNode($propName, $info->type, $method->name);
        } elseif ('link' === $info->type) {
            if (!$this->canSerializeEntity($info->targetEntity)) {
                return null;
            }

            $relation = new RelationNode();
            $relation->entity = $info->targetEntity;
            $relation->isSingleValued = $info->singleValued;
            $relation->name = $propName;
            $relation->getter = $method->name;

            return $relation;
        }

        return null;
    }

    /**
     * Returns a list of class fields.
     *
     * @param string $className
     *
     * @throws \ReflectionException if class is absent
     *
     * @return array
     */
    public function getFieldsForClass(string $className): array
    {
        $reflection = new \ReflectionClass($className);
        $properties = $reflection->getProperties();
        /** @var FieldNode[] $fields */
        $fields = [];
        /** @var RelationNode[] $links */
        $links = [];

        $apply = function ($node) use (&$fields, &$links) {
            if (!$node) {
                return;
            }

            if ($node instanceof RelationNode) {
                $links[] = $node;
            } else {
                $fields[] = $node;
            }
        };

        $classMeta = $this->_em->getClassMetadata($className);

        foreach ($properties as $prop) {
            if ($this->isIgnored($prop)) {
                continue;
            }

            $apply($this->handleProperty($prop, $classMeta));
        }

        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (!($name = $this->getVirtualPropertyName($method))) {
                continue;
            }

            $apply($this->handleVirtualProperty($name, $method));
        }

        return [$fields, $links];
    }

    public function loadSerializer()
    {
        $name = $this->getClassName();
        $file = $this->_cachePath.DIRECTORY_SEPARATOR.$name.'.php';
        require_once $file;

        return new $name();
    }

    /**
     * Generates the serializer class.
     *
     * @param $force - Force rebuild of class
     *
     * @throws RenderFailedException  if can't render file
     * @throws ClassNotFoundException if some entity class was not found
     * @throws SampleObjectException
     */
    public function generateService($force = false)
    {
        $classes = [];

        $name = $this->getClassName();

        if ($force || $this->fileShouldBeRebuilt()) {
            $this->checkSamples();
            foreach ($this->_configService->getEntities() as $entity => $_) {
                $classes[$entity] = $this->generate($entity);
            }

            $content = $this->render('serializer.php.twig', [
                'classes' => $classes,
                'entities' => $this->_configService->getEntities(),
                'name' => $name,
            ]);
            if (!file_exists($this->_cachePath)) {
                mkdir($this->_cachePath);
            }
            file_put_contents($this->_cachePath.DIRECTORY_SEPARATOR.$name.'.php', $content);
        }
    }

    /**
     * Check entities maps if they are correct.
     *
     * @throws SampleObjectException if at least one map can't be transformed to an object
     */
    public function checkSamples()
    {
        foreach ($this->_configService->getEntities() as $entity => $attributes) {
            $samples = $attributes['samples'];

            foreach ($samples as $name => $sample) {
                $obj = json_decode($sample);

                if (!$obj) {
                    throw new SampleObjectException($entity, $name);
                }
            }
        }
    }

    protected function getClassName()
    {
        return 'Serializer'.$this->_configService->getConfigHash();
    }

    protected function fileShouldBeRebuilt()
    {
        if ('prod' === $this->_environment) {
            return false;
        }
        $name = $this->getClassName();
        if (!file_exists($this->_cachePath)) {
            $files = [];
        } else {
            $files = array_filter(scandir($this->_cachePath), function ($i) {
                return '.' !== $i && '..' !== $i && strpos($i, 'php');
            });
        }

        foreach ($files as $i => $file) {
            if ($file !== $name.'.php') {
                unlink($this->_cachePath.DIRECTORY_SEPARATOR.$file);
                unset($files[$i]);
            }
        }

        return empty($files);
    }

    /**
     * Checks if serializer can serialize entity.
     *
     * @param $entity
     *
     * @return bool
     */
    protected function canSerializeEntity($entity)
    {
        return isset($this->_configService->getEntities()[$entity]);
    }

    /**
     * @param $template - template name relative to bundle Resources/views
     * @param $vars - variables accessible from template
     *
     * @throws RenderFailedException if an error occurred
     *
     * @return string
     */
    protected function render($template, $vars)
    {
        try {
            $loader = new \Twig_Loader_Filesystem(__DIR__.'/../Resources/views');
            $twig = new \Twig_Environment($loader);

            return $twig->render($template, $vars);
        } catch (\Exception $ex) {
            throw new RenderFailedException($template, $ex);
        }
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
    protected function getRealGetterName(\ReflectionProperty $prop, bool $isLink, bool $isSingle): string
    {
        $annotation = $this->_reader->getPropertyAnnotation($prop, $this->_configService->getGetterAnnotation());

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
    protected function getSerializedName(\ReflectionProperty $prop): string
    {
        $annotation = $this->_reader->getPropertyAnnotation($prop, $this->_configService->getNameAnnotation());

        return $annotation ? $annotation->name : $prop->name;
    }

    /**
     * Returns if the property should be ignored.
     *
     * @param \ReflectionProperty $prop
     *
     * @return bool
     */
    protected function isIgnored(\ReflectionProperty $prop): bool
    {
        $annotation = $this->_reader->getPropertyAnnotation($prop, $this->_configService->getIgnoreAnnotation());

        return (bool) $annotation;
    }

    /**
     * Creates node for embedded object.
     *
     * @param \ReflectionProperty $prop
     * @param ClassMetadata       $classMeta
     *
     * @throws \ReflectionException
     *
     * @return FieldNode
     */
    protected function createEmbeddedNode(\ReflectionProperty $prop, ClassMetadata $classMeta): FieldNode
    {
        $field = $this->createFieldNode($prop, 'embed', $this->getSerializedName($prop));
        $embeddings = $classMeta->embeddedClasses;
        list($field->fields, $field->relations) = $this->getFieldsForClass($embeddings[$prop->name]['class']);

        return $field;
    }

    /**
     * Creates a relation node for the property.
     *
     * @param \ReflectionProperty $prop
     * @param ClassMetadata       $classMeta
     *
     * @return RelationNode|null
     */
    protected function createRelationNode(\ReflectionProperty $prop, ClassMetadata $classMeta)
    {
        $mappings = $classMeta->associationMappings;
        $entity = $mappings[$prop->name]['targetEntity'];

        if (!$this->canSerializeEntity($entity)) {
            return null;
        }

        $relation = new RelationNode();
        $relation->entity = $entity;
        $relation->isSingleValued = $classMeta->isSingleValuedAssociation($prop->name);
        $relation->name = $this->getSerializedName($prop);
        $relation->getter = $this->getRealGetterName($prop, true, $relation->isSingleValued);

        return $relation;
    }

    /**
     * Creates the field node object.
     *
     * @param \ReflectionProperty $name
     * @param string              $type
     * @param string              $serializedName
     *
     * @return FieldNode
     */
    protected function createFieldNode(\ReflectionProperty $name, string $type, string $serializedName): FieldNode
    {
        $field = new FieldNode();
        $field->type = $type;
        $field->getter = $this->getRealGetterName($name, false, true);
        $field->name = $serializedName;

        return $field;
    }

    /**
     * Creates a node based on a real property of the entity.
     *
     * @param \ReflectionProperty $prop
     * @param ClassMetadata       $classMeta
     *
     * @throws \ReflectionException
     *
     * @return FieldNode|RelationNode|null
     */
    protected function handleProperty(\ReflectionProperty $prop, ClassMetadata $classMeta)
    {
        $embeddings = $classMeta->embeddedClasses;

        if (isset($embeddings[$prop->name])) {
            return $this->createEmbeddedNode($prop, $classMeta);
        }

        $mappings = $classMeta->associationMappings;

        if (isset($mappings[$prop->name])) {
            return $this->createRelationNode($prop, $classMeta);
        }

        $fields = $classMeta->fieldMappings;
        $type = $fields[$prop->name]['type'] ?? '';
        $serializedName = $this->getSerializedName($prop);

        return $this->createFieldNode($prop, $type, $serializedName);
    }

    /**
     * Returns the virtual property name.
     *
     * @param \ReflectionMethod $method
     *
     * @return string|bool
     */
    protected function getVirtualPropertyName(\ReflectionMethod $method)
    {
        $annotation = $this->_reader->getMethodAnnotation($method, $this->_configService->getVirtualAnnotation());

        return $annotation->name ?? null;
    }

    /**
     * Creates virtual field node.
     *
     * @param string $name - the name of node
     * @param string $type - the typ of node
     * @param $method - the method title
     *
     * @return FieldNode
     */
    protected function createVirtualFieldNode(string $name, string $type, string $method = null): FieldNode
    {
        $field = new FieldNode();
        $field->type = $type;
        $field->name = $name;
        $field->getter = $method;

        return $field;
    }

    /**
     * Generates a function which contains the normalization logic for the current entity.
     *
     * @param string $className - Doctrine entity FQCN
     *
     * @throws ClassNotFoundException if class cant be loaded
     * @throws RenderFailedException  if can't render template
     *
     * @return string - php function text representation
     */
    protected function generate(string $className): string
    {
        try {
            list($fields, $links) = $this->getFieldsForClass($className);
            $name = explode('/', $className);
            $short_name = end($name);
            $class = ucfirst($short_name).'Serializer';
            $content = $this->render('function.php.twig', [
                'class' => $class,
                'fields' => $fields,
                'links' => $links,
                'var' => 'var',
                'object' => 'object',
                'return' => true,
            ]);

            return $content;
        } catch (\ReflectionException $ex) {
            throw new ClassNotFoundException($className);
        }
    }
}
