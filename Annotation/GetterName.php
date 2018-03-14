<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Annotation;

/**
 * GetterName.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class GetterName
{
    /**
     * @var string
     */
    public $value;
}
