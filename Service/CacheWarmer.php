<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Service;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Class CacheWarmer responsible for generation of actual serializer at cache warmup.
 */
class CacheWarmer implements CacheWarmerInterface
{
    private $_generator;

    public function __construct(ClassGenerator $generator)
    {
        $this->_generator = $generator;
    }

    public function isOptional()
    {
        return false;
    }

    public function warmUp($cacheDir)
    {
        $this->_generator->generateService(true);
    }
}
