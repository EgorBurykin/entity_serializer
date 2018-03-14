<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Nodes;

class FieldNode extends GeneratorNode
{
    public $type;
    /** @var FieldNode[] $fields */
    public $fields = [];
    /** @var RelationNode[] $relations */
    public $relations = [];
}
