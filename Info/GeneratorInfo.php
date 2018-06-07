<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Info;

class GeneratorInfo
{
    public $getter;
    public $name;

    public function __construct($name = null, $getter = null)
    {
        $this->name = $name;
        $this->getter = $getter;
    }
}
