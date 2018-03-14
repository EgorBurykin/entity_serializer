<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Exception;

class ClassNotFoundException extends \Exception
{
    const MESSAGE = 'Class %s not found. You probably have broken configuration for the serializer.';

    public function __construct(string $className)
    {
        parent::__construct(sprintf(static::MESSAGE, $className));
    }
}
