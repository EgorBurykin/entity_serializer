<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Exception;

class AbstractStaticCallException extends \Exception
{
    const MESSAGE = 'You just called static method %s which should be unaccessible';

    public function __construct(string $callName)
    {
        parent::__construct(sprintf(static::MESSAGE, $callName));
    }
}
