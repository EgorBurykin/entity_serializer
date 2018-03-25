<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Jett\JSONEntitySerializerBundle\Exception\AbstractStaticCallException;
use Jett\JSONEntitySerializerBundle\Nodes\Node;
use Jett\JSONEntitySerializerBundle\Transformer\TransformerInterface;

/**
 * This class serializes doctrine classic entity to JSON.
 *
 * It goes in to four steps:
 *
 * 1) It builds normalized object tree having only nodes accessible on provided sample.
 * Tree's node represents class Node storing data in a way allowing caching normalized data for every unique entity.
 * Every unique entity for unique sample will be normalized once. According to mostly run and die nature of
 * PHP there will be only one sample for entity per request.
 * So memory O(n*m) m ~ 1 (n - count of unique entities, m - count of samples used in current request) is enough for
 * run and die processes. And on other side it gives us opportunity to use random samples for daemons using this bundle.
 *
 * @see Node
 *
 * 2) It compiles data from this tree.
 * @see BaseSerializer::compile()
 *
 * 3) It takes normalized object and calls json_encode on it.
 * @see BaseSerializer::serialize()
 *
 * This class will be extended with code generator with help of this bundle.
 *
 * @todo Use Transformers
 */
abstract class BaseSerializer
{
    public static $_cache = [];

    protected static $_hashes = [];

    protected static $_samples = [];

    protected $_transformers = [];

    /**
     * Cleans cache.
     */
    public static function cleanCache()
    {
        self::$_cache = [];
    }

    public static function setSamples($samples) {
        //TODO: write it
    }

    /**
     * Adds transformer to pool
     * @param TransformerInterface $transformer
     */
    public function addTransformer(TransformerInterface $transformer)
    {
        $this->_transformers[$transformer->getId()] = $transformer;
    }

    public function transform($value, $defaultTransformer, $currentTransformer)
    {
        //TODO: write it
    }
    /**
     * Converts entity to plain object containing properties according to sample.
     *
     * @param $entity - Doctrine entity
     * @param object|string|null $sample - sample object or it's title from configuration or null, to use default
     *
     * @return object[]|object
     */
    public function toPureObject($entity, $sample = null)
    {
        if (is_scalar($entity) || null === $entity || $entity instanceof \stdClass) {
            return $entity;
        }
        if (is_array($entity) || $entity instanceof Collection) {
            $objects = [];
            foreach ($entity as $o) {
                $objects[] = $this->toPureObject($o, $sample);
            }

            return $objects;
        }
        $class = ClassUtils::getRealClass(get_class($entity));
        if (!$sample) {
            $sample = 'default';
        }
        if (is_string($sample)) {
            $sample = 'all' === $sample ? $sample : $this->getSample($class, $sample);
        }
        $node = $this->getNode($entity, $sample, $class);

        return $this->compile($node, $sample);
    }

    /**
     * Serializes entity to json.
     *
     * @param $entity
     * @param null $sample
     *
     * @return string
     */
    public function serialize($entity, $sample = null)
    {
        $pureObject = $this->toPureObject($entity, $sample);
        $str = json_encode($pureObject);

        return $str;
    }

    /**
     * Invoke node from cache.
     *
     * @param $id - ID of cached entity
     * @param string $entityFQCN - FQCN of entity
     * @param string $sampleHash - sample hash
     *
     * @return Node|null
     */
    protected static function cached($id, string $entityFQCN, string $sampleHash)
    {
        return self::$_cache[$entityFQCN][$id][$sampleHash] ?? null;
    }

    /**
     * Recursively compiles tree to object.
     *
     * @param Node          $node
     * @param string|object $sample
     *
     * @return null|object
     */
    protected function compile(Node $node = null, $sample)
    {
        if (!$node) {
            return null;
        }
        $obj = $node->object;
        if ('id' === $sample) {
            return $obj;
        }
        foreach ($node->links as $key => $link) {
            if (is_array($link)) {
                $obj->$key = [];
                foreach ($link as $item) {
                    if ('all' === $sample) {
                        $obj->$key[] = $this->compile($item, 'id');
                    } elseif ('id' !== $sample) {
                        $obj->$key[] = self::compile($item, $sample->$key);
                    }
                }
            } else {
                if ('all' === $sample) {
                    $obj->$key = $this->compile($link, 'id');
                } elseif ('id' !== $sample) {
                    $obj->$key = $this->compile($link, $sample->$key);
                }
            }
        }

        return $obj;
    }

    /**
     * Caches node.
     *
     * @param Node $node
     * @param $id
     * @param $entityFQCN
     * @param $sampleHash
     */
    protected static function cache(Node $node, $id, $entityFQCN, $sampleHash)
    {
        if (!isset(self::$_cache[$entityFQCN])) {
            self::$_cache[$entityFQCN] = [];
        }
        if (!isset(self::$_cache[$entityFQCN][$id])) {
            self::$_cache[$entityFQCN][$id] = [];
        }
        self::$_cache[$entityFQCN][$id][$sampleHash] = $node;
    }

    /**
     * Gets object tree calling itself recursively trusting normalization of entity and recursive calls to
     * generated part of this class.
     *
     * @see BaseSerializer::toPlainJSON()
     *
     * @param $entity - Doctrine entity object with getId() method, returning scalar id
     * @param $sample - sample object or it's title. Samples provided by configuration of bundle
     * @param $entityFQCN - FQCN of entity
     *
     * @return Node|null
     */
    protected function getNode($entity, &$sample, $entityFQCN)
    {
        if (empty($entity)) {
            return null;
        }
        $hash = is_string($sample) ? $sample : spl_object_hash($sample);
        if ($node = self::cached($entity->getId(), $entityFQCN, $hash)) {
            return $node;
        }
        $node = $this->toPlainJSON($entity, $sample, $entityFQCN);
        self::cache($node, $entity->getId(), $entityFQCN, $hash);

        return $node;
    }

    /**
     * Performs all of hard work. Will be generated by this bundle.
     *
     * @param $entity
     * @param $sample
     * @param string $entityFQCN
     *
     * @return Node
     */
    abstract protected function toPlainJSON($entity, &$sample, string $entityFQCN);

    /**
     * Returns sample object for className according to sample object's title
     * This method will be generated.
     *
     * @param $className
     * @param $sampleTitle
     *
     * @return object|string
     */
    public static function getSample($className, $sampleTitle) {
        if (!isset(self::$_samples[$className.$sampleTitle])) {
            return 'all';
        }
        return self::$_samples[$className.$sampleTitle];
    }
}
