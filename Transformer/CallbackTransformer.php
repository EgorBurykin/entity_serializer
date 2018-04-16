<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Transformer;

class CallbackTransformer implements TransformerInterface
{
    private $callback;

    private $id;

    /**
     * EntityIdTransformer constructor.
     *
     * @param callable $callback
     * @param $id
     */
    public function __construct(callable $callback, $id)
    {
        $this->callback = $callback;
        $this->id = $id;
    }

    public function transform($data)
    {
        $call = $this->callback;

        return $call($data);
    }

    public function getId()
    {
        return $this->id;
    }
}
