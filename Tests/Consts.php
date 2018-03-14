<?php

namespace Jett\JSONEntitySerializerBundle\Tests;

class Consts
{
    const ID = 1;
    const TITLE = 'title';
    const DATE = '2017-01-01T00:00:00+00:00';

    public static function date()
    {
        return \DateTime::createFromFormat(DATE_W3C, self::DATE);
    }
}
