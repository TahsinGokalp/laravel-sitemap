<?php

return [

    /*
    | Enable or disable cache.
    */
    'use_cache' => false,

    /*
     | Unique cache key.
     */
    'cache_key' => 'laravel-sitemap.'.config('app.url'),

    /*
     | Cache duration, can be int or timestamp.
     */
    'cache_duration' => 3600,

    /*
     | Escaping html entities.
     */
    'escaping' => true,

    /*
     | Use limitSize() for big sitemaps.
     */
    'use_limit_size' => false,

    /*
     | Custom max size for limitSize().
     */
    'max_size' => null,

    /*
     | Enable or disable xsl styles.
     */
    'use_styles' => true,

    /*
     | Set custom location for xsl styles (must end with slash).
     */
    'styles_location' => '/vendor/sitemap/styles/',

    /*
     | Use gzip compression.
     */
    'use_gzip' => false,

];
