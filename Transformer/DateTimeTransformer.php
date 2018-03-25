<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Transformer;


class DateTimeTransformer implements TransformerInterface
{

    private $format;

    public function __construct($format = \DateTime::W3C)
    {
        $this->format = $format;
    }

    const TYPES = ['datetime','date','datetime_immutable','datetimetz','datetimetz_immutable'];

    public function supports($type = null)
    {
        return isset(static::TYPES[strtolower($type)]);
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