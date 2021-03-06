<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Nodes;

/**
 * Class represent node - an abstraction helping to store information about relations in object tree.
 */
class Node
{
    /**
     * Object relations. Associative array of Nodes indexed by property names.
     *
     * @var array
     */
    public $links = [];

    /**
     * Normalized object without relations.
     *
     * @var object
     */
    public $object;

    public function __construct($object, $links = [])
    {
        $this->object = $object;
        $this->links = $links;
    }
}
