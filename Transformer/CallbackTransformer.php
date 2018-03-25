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

    private $type;

    private $id;

    /**
     * EntityIdTransformer constructor.
     * @param callable $callback
     * @param string $type
     * @param $id
     */
    public function __construct(callable $callback, $id, $type = null)
    {
        $this->callback = $callback;
        $this->type = $type;
        $this->id = $id;
    }

    public function supports($type = null)
    {
        return $this->type? $type === $this->type: true;
    }

    public function transform($data)
    {
        return $this->callback($data);
    }

    public function getId()
    {
        return $this->id;
    }

}