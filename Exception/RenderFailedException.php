<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Exception;

class RenderFailedException extends \Exception
{
    public function __construct(string $template, \Throwable $prev)
    {
        parent::__construct(
            sprintf('Render failed', $template),
            null,
            $prev
        );
    }
}
