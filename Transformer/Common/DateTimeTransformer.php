<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Transformer\Common;

use Jett\JSONEntitySerializerBundle\Transformer\TransformerInterface;

class DateTimeTransformer implements TransformerInterface
{
    const TYPES = ['datetime', 'date', 'datetime_immutable', 'datetimetz', 'datetimetz_immutable'];

    private $format;

    public function __construct($format = \DateTime::W3C)
    {
        $this->format = $format;
    }

    public function transform($data)
    {
        if (!$data instanceof \DateTime) {
            return null;
        }

        return $data->format($this->format);
    }

    public function getId()
    {
        return 'datetime';
    }
}
