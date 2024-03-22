<?php

namespace TahsinGokalp\Sitemap;

use Carbon\Carbon;
use DateInterval;
use DateTime;
use DateTimeInterface;

class Model
{
    public bool $testing = false;

    private array $items = [];

    private array $sitemaps = [];

    private ?string $title = null;

    private ?string $link = null;

    private bool $useStyles = true;

    private ?string $sloc = '/vendor/sitemap/styles/';

    private bool $useCache = false;

    private string $cacheKey = 'laravel-sitemap.';

    private DateInterval|DateTimeInterface|int|null $cacheDuration = 3600;

    private bool $escaping = true;

    private bool $useLimitSize = false;

    private ?int $maxSize = null;

    private bool $useGzip = false;

    /**
     * Populating model variables from configuration file.
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
    public function getSloc(): ?string
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
    public function getCacheDuration(): DateInterval|DateTimeInterface|int|null
    {
        return $this->cacheDuration;
    }

    /**
     | Returns $escaping value.
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
     */
    public function getMaxSize(): ?int
    {
        return $this->maxSize;
    }

    /**
     * Returns $useGzip value.
     */
    public function getUseGzip(): bool
    {
        return $this->useGzip;
    }

    /**
     * Sets $escaping value.
     */
    public function setEscaping(bool $escaping): void
    {
        $this->escaping = $escaping;
    }

    /**
     * Adds item to $items array.
     */
    public function setItems(array $items): void
    {
        $this->items[] = $items;
    }

    /**
     * Adds sitemap to $sitemaps array.
     */
    public function setSitemaps(array $sitemap): void
    {
        $this->sitemaps[] = $sitemap;
    }

    /**
     * Sets $title value.
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Sets $link value.
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * Sets $useStyles value.
     */
    public function setUseStyles(bool $useStyles): void
    {
        $this->useStyles = $useStyles;
    }

    /**
     * Sets $sloc value.
     */
    public function setSloc(?string $sloc): void
    {
        $this->sloc = $sloc;
    }

    /**
     * Sets $useLimitSize value.
     */
    public function setUseLimitSize(bool $useLimitSize): void
    {
        $this->useLimitSize = $useLimitSize;
    }

    /**
     * Sets $maxSize value.
     */
    public function setMaxSize(int $maxSize): void
    {
        $this->maxSize = $maxSize;
    }

    /**
     * Sets $useGzip value.
     */
    public function setUseGzip(bool $useGzip = true): void
    {
        $this->useGzip = $useGzip;
    }

    /**
     * Limit size of $items array to 50000 elements (1000 for google-news).
     */
    public function limitSize(int $max = 50000): void
    {
        $this->items = array_slice($this->items, 0, $max);
    }

    /**
     * Reset $items array.
     */
    public function resetItems(array $items = []): void
    {
        $this->items = $items;
    }

    /**
     * Reset $sitemaps array.
     */
    public function resetSitemaps(array $sitemaps = []): void
    {
        $this->sitemaps = $sitemaps;
    }

    /**
     * Set use cache value.
     */
    public function setUseCache(bool $useCache = true): void
    {
        $this->useCache = $useCache;
    }

    /**
     * Set cache key value.
     */
    public function setCacheKey(string $cacheKey): void
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
