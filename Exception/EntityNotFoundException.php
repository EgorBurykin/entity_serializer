<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Exception;

class EntityNotFoundException extends \Exception
{
    const MESSAGE = 'Serializer cant\'t find handler for entity %s. You might add this class to config';

    public function __construct(string $entity)
    {
        parent::__construct(sprintf(static::MESSAGE, $entity));
    }
}
