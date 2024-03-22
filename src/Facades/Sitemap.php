<?php

namespace TahsinGokalp\Sitemap\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TahsinGokalp\Sitemap\Sitemap
 */
class Sitemap extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TahsinGokalp\Sitemap\Sitemap::class;
    }
}
