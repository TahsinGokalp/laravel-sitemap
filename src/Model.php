<?php

namespace TahsinGokalp\Sitemap;

use Carbon\Carbon;
use DateTime;

class Model
{
    public bool $testing = false;

    private array $items = [];

    private array $sitemaps = [];

    private ?string $title = null;

    private ?string $link = null;

    private bool $useStyles = true;

    private string $sloc = '/vendor/sitemap/styles/';

    private bool $useCache = false;

    private string $cacheKey = 'laravel-sitemap.';

    private Carbon|Datetime|int $cacheDuration = 3600;

    private bool $escaping = true;

    private bool $useLimitSize = false;

    private ?bool $maxSize = null;

    private bool $useGzip = false;

    /*
     | Populating model variables from configuration file.
     */
    public function __construct(array $config)
    {
        $this->useCache = $config['use_cache'] ?? $this->useCache;
        $this->cacheKey = $config['cache_key'] ?? $this->cacheKey;
        $this->cacheDuration = $config['cache_duration'] ?? $this->cacheDuration;
        $this->escaping = $config['escaping'] ?? $this->escaping;
        $this->useLimitSize = $config['use_limit_size'] ?? $this->useLimitSize;
        $this->useStyles = $config['use_styles'] ?? $this->useStyles;
        $this->sloc = $config['styles_location'] ?? $this->sloc;
        $this->maxSize = $config['max_size'] ?? $this->maxSize;
        $this->testing = $config['testing'] ?? $this->testing;
        $this->useGzip = $config['use_gzip'] ?? $this->useGzip;
    }

    /**
     * Returns $items array.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Returns $sitemaps array.
     */
    public function getSitemaps(): array
    {
        return $this->sitemaps;
    }

    /**
     * Returns $title value.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Returns $link value.
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Returns $useStyles value.
     */
    public function getUseStyles(): bool
    {
        return $this->useStyles;
    }

    /**
     * Returns $sloc value.
     */
    public function getSloc(): string
    {
        return $this->sloc;
    }

    /**
     * Returns $useCache value.
     */
    public function getUseCache(): bool
    {
        return $this->useCache;
    }

    /**
     * Returns $CacheKey value.
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * Returns $CacheDuration value.
     */
    public function getCacheDuration(): string
    {
        return $this->cacheDuration;
    }

    /**
     * Returns $escaping value.
     */
    public function getEscaping(): bool
    {
        return $this->escaping;
    }

    /**
     * Returns $useLimitSize value.
     */
    public function getUseLimitSize(): bool
    {
        return $this->useLimitSize;
    }

    /**
     * Returns $maxSize value.
     *
     * @param  int  $maxSize
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * Returns $useGzip value.
     *
     * @param  bool  $useGzip
     */
    public function getUseGzip()
    {
        return $this->useGzip;
    }

    /**
     * Sets $escaping value.
     *
     * @param  bool  $escaping
     */
    public function setEscaping($b)
    {
        $this->escaping = $b;
    }

    /**
     * Adds item to $items array.
     *
     * @param  array  $item
     */
    public function setItems($items)
    {
        $this->items[] = $items;
    }

    /**
     * Adds sitemap to $sitemaps array.
     *
     * @param  array  $sitemap
     */
    public function setSitemaps($sitemap)
    {
        $this->sitemaps[] = $sitemap;
    }

    /**
     * Sets $title value.
     *
     * @param  string  $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Sets $link value.
     *
     * @param  string  $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Sets $useStyles value.
     *
     * @param  bool  $useStyles
     */
    public function setUseStyles($useStyles)
    {
        $this->useStyles = $useStyles;
    }

    /**
     * Sets $sloc value.
     *
     * @param  string  $sloc
     */
    public function setSloc($sloc)
    {
        $this->sloc = $sloc;
    }

    /**
     * Sets $useLimitSize value.
     *
     * @param  bool  $useLimitSize
     */
    public function setUseLimitSize($useLimitSize)
    {
        $this->useLimitSize = $useLimitSize;
    }

    /**
     * Sets $maxSize value.
     *
     * @param  int  $maxSize
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * Sets $useGzip value.
     *
     * @param  bool  $useGzip
     */
    public function setUseGzip($useGzip = true)
    {
        $this->useGzip = $useGzip;
    }

    /**
     * Limit size of $items array to 50000 elements (1000 for google-news).
     */
    public function limitSize($max = 50000)
    {
        $this->items = array_slice($this->items, 0, $max);
    }

    /**
     * Reset $items array.
     *
     * @param  array  $items
     */
    public function resetItems($items = [])
    {
        $this->items = $items;
    }

    /**
     * Reset $sitemaps array.
     *
     * @param  array  $sitemaps
     */
    public function resetSitemaps($sitemaps = [])
    {
        $this->sitemaps = $sitemaps;
    }

    /**
     * Set use cache value.
     *
     * @param  bool  $useCache
     */
    public function setUseCache($useCache = true)
    {
        $this->useCache = $useCache;
    }

    /**
     * Set cache key value.
     *
     * @param  string  $cacheKey
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    /**
     * Set cache duration value.
     */
    public function setCacheDuration(DateTime|int|Carbon $cacheDuration): void
    {
        $this->cacheDuration = $cacheDuration;
    }

}
