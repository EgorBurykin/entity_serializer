<?php
/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Transformer;

/**
 * This is common interface for transformers. We can use transformer when provide sample object or configure
 * transformers section of bundle.
 *
 * @package Jett\JSONEntitySerializerBundle\Transformer
 */
interface TransformerInterface
{
    public function transform($data);

    public function getId();
}