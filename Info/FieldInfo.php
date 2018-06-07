<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Info;


class FieldInfo extends GeneratorInfo
{
    public $type;
    /** @var FieldInfo[] $fields */
    public $fields = [];
    /** @var RelationInfo[] $relations */
    public $relations = [];

    public function __construct($name = null, $type = null, $getter = null, array $fields = [], array $relations = [])
    {
        parent::__construct($name, $getter);
        $this->type = $type;
        $this->fields = $fields;
        $this->relations = $relations;
    }
}
