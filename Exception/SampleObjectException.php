<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Exception;

class SampleObjectException extends \Exception
{
    const MESSAGE = 'Sample object for %s with name %s is incorrect.';

    public function __construct(string $entity, string $title)
    {
        parent::__construct(sprintf(static::MESSAGE, $entity, $title));
    }
}
