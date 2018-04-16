<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Service;
use Jett\JSONEntitySerializerBundle\Transformer\TransformerInterface;

/**
 * Interface SerializerInterface.
 *
 * @todo: Doc
 */
interface SerializerInterface
{
    public function toPureObject($entity, $sample = null);

    public function serialize($entity, $sample = null);

    public function clearCache();

    public function addTransformer(TransformerInterface $transformer);

}
