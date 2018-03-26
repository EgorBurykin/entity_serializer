<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Transformer\Common;


use Jett\JSONEntitySerializerBundle\Transformer\TransformerInterface;

class UpperTransformer implements TransformerInterface
{
    public function supports($type = null)
    {
        return true;
    }

    public function transform($data)
    {
        return strtoupper($data);
    }

    public function getId()
    {
        return 'upper';
    }

}