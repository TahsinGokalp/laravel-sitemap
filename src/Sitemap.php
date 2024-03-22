<?php

namespace TahsinGokalp\Sitemap;

use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Psr\SimpleCache\InvalidArgumentException;

class Sitemap
{
    /**
     * Model instance.
     */
    public Model $model;

    /**
     * CacheRepository instance.
     */
    public CacheRepository $cache;

    /**
     * ConfigRepository instance.
     */
    protected ConfigRepository $configRepository;

    /**
     * Filesystem instance.
     */
    protected Filesystem $file;

    /**
     * ResponseFactory instance.
     */
    protected ResponseFactory $response;

    /**
     * ViewFactory instance.
     */
    protected ViewFactory $view;

    /**
     * Using constructor we populate our model from configuration file and loading dependencies.
     */
    public function __construct(array $config, CacheRepository $cache, ConfigRepository $configRepository,
        Filesystem $file, ResponseFactory $response, ViewFactory $view)
    {
        $this->cache = $cache;
        $this->configRepository = $configRepository;
        $this->file = $file;
        $this->response = $response;
        $this->view = $view;

        $this->model = new Model($config);
    }

    /**
     * Set cache options.
     */
    public function setCache(?string $key = null, Carbon|Datetime|int|null $duration = null, bool $useCache = true): void
    {
        $this->model->setUseCache($useCache);

        if ($key !== null) {
            $this->model->setCacheKey($key);
        }

        if ($duration !== null) {
            $this->model->setCacheDuration($duration);
        }
    }

    /**
     * Checks if content is cached.
     */
    public function isCached(): bool
    {
        try {
            return $this->model->getUseCache() && $this->cache->has($this->model->getCacheKey());
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    /**
     * Add new sitemap item to $items array.
     */
    public function add(string $loc, ?string $lastmod = null, ?string $priority = null, ?string $freq = null,
        array $images = [], ?string $title = null, array $translations = [], array $videos = [],
        array $googlenews = [], array $alternates = []): void
    {
        $params = [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'priority' => $priority,
            'freq' => $freq,
            'images' => $images,
            'title' => $title,
            'translations' => $translations,
            'videos' => $videos,
            'googlenews' => $googlenews,
            'alternates' => $alternates,
        ];

        $this->addItem($params);
    }

    /**
     * Add new sitemap one or multiple items to $items array.
     */
    public function addItem(array $params = []): void
    {

        // if is multidimensional
        if (array_key_exists(1, $params)) {
            foreach ($params as $a) {
                $this->addItem($a);
            }

            return;
        }

        // set default values
        $loc = $params['loc'] ?? '/';

        $lastmod = $params['lastmod'] ?? null;

        $priority = $params['priority'] ?? null;

        $freq = $params['freq'] ?? null;

        $title = $params['title'] ?? null;

        $images = $params['images'] ?? [];

        $translations = $params['translations'] ?? [];

        $alternates = $params['alternates'] ?? [];

        $videos = $params['videos'] ?? [];

        $googlenews = $params['googlenews'] ?? [];

        // escaping
        if ($this->model->getEscaping()) {
            $loc = htmlentities($loc, ENT_XML1);

            if ($title !== null) {
                $title = htmlentities($title, ENT_XML1);
            }

            if ($images) {
                foreach ($images as $k => $image) {
                    foreach ($image as $key => $value) {
                        $images[$k][$key] = htmlentities($value, ENT_XML1);
                    }
                }
            }

            if ($translations) {
                foreach ($translations as $k => $translation) {
                    foreach ($translation as $key => $value) {
                        $translations[$k][$key] = htmlentities($value, ENT_XML1);
                    }
                }
            }

            if ($alternates) {
                foreach ($alternates as $k => $alternate) {
                    foreach ($alternate as $key => $value) {
                        $alternates[$k][$key] = htmlentities($value, ENT_XML1);
                    }
                }
            }

            if ($videos) {
                foreach ($videos as $k => $video) {
                    if (! empty($video['title'])) {
                        $videos[$k]['title'] = htmlentities($video['title'], ENT_XML1);
                    }
                    if (! empty($video['description'])) {
                        $videos[$k]['description'] = htmlentities($video['description'], ENT_XML1);
                    }
                }
            }

            if ($googlenews && isset($googlenews['sitename'])) {
                $googlenews['sitename'] = htmlentities($googlenews['sitename'], ENT_XML1);
            }
        }

        $googlenews['sitename'] = $googlenews['sitename'] ?? '';
        $googlenews['language'] = $googlenews['language'] ?? 'en';
        $googlenews['publication_date'] = $googlenews['publication_date'] ?? date('Y-m-d H:i:s');

        $this->model->setItems([
            'loc' => $loc,
            'lastmod' => $lastmod,
            'priority' => $priority,
            'freq' => $freq,
            'images' => $images,
            'title' => $title,
            'translations' => $translations,
            'videos' => $videos,
            'googlenews' => $googlenews,
            'alternates' => $alternates,
        ]);
    }

    /**
     * Add new sitemap to $sitemaps array.
     */
    public function addSitemap(string $loc, ?string $lastmod = null): void
    {
        $this->model->setSitemaps([
            'loc' => $loc,
            'lastmod' => $lastmod,
        ]);
    }

    /**
     * Add new sitemap to $sitemaps array.
     */
    public function resetSitemaps(array $sitemaps = []): void
    {
        $this->model->resetSitemaps($sitemaps);
    }

    /**
     * Returns document with all sitemap items from $items array.
     * Format Options: xml, html, txt, ror-rss, ror-rdf, google-news
     *
     * @throws InvalidArgumentException
     */
    public function render(string $format = 'xml'): Response
    {
        // limit size of sitemap
        if ($this->model->getMaxSize() > 0 && count($this->model->getItems()) > $this->model->getMaxSize()) {
            $this->model->limitSize($this->model->getMaxSize());
        } elseif ($format === 'google-news' && count($this->model->getItems()) > 1000) {
            $this->model->limitSize(1000);
        } elseif ($format !== 'google-news' && count($this->model->getItems()) > 50000) {
            $this->model->limitSize();
        }

        $data = $this->generate($format);

        return $this->response->make($data['content'], 200, $data['headers']);
    }

    /**
     * Generates document with all sitemap items from $items array.
     * Format Options: xml, html, txt, ror-rss, ror-rdf, sitemapindex, google-news
     *
     * @throws InvalidArgumentException
     */
    public function generate(string $format = 'xml'): array
    {
        // check if caching is enabled, there is a cached content and its duration isn't expired
        if ($this->isCached()) {
            ($format === 'sitemapindex')
                ? $this->model->resetSitemaps((array)$this->cache->get($this->model->getCacheKey()))
                : $this->model->resetItems((array)$this->cache->get($this->model->getCacheKey()));
        } elseif ($this->model->getUseCache()) {
            ($format === 'sitemapindex')
                ? $this->cache->put($this->model->getCacheKey(), $this->model->getSitemaps(), $this->model->getCacheDuration())
                : $this->cache->put($this->model->getCacheKey(), $this->model->getItems(), $this->model->getCacheDuration());
        }

        if (! $this->model->getLink()) {
            $appUrl = $this->configRepository->get('app.url');
            if(is_string($appUrl)){
                $this->model->setLink($appUrl);
            }
        }

        if (! $this->model->getTitle()) {
            $this->model->setTitle('Sitemap for '.$this->model->getLink());
        }

        $channel = [
            'title' => $this->model->getTitle(),
            'link' => $this->model->getLink(),
        ];

        // check if styles are enabled
        if ($this->model->getUseStyles() && $this->model->getSloc() !== null
            && file_exists(public_path($this->model->getSloc().$format.'.xsl'))) {
            // use style from your custom location
            $style = $this->model->getSloc().$format.'.xsl';
        } else {
            // don't use style
            $style = null;
        }

        return match ($format) {
            'ror-rss' => ['content' => $this->view->make('sitemap::ror-rss', ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/rss+xml; charset=utf-8']],
            'ror-rdf' => ['content' => $this->view->make('sitemap::ror-rdf', ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/rdf+xml; charset=utf-8']],
            'html' => ['content' => $this->view->make('sitemap::html', ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/html; charset=utf-8']],
            'txt' => ['content' => $this->view->make('sitemap::txt', ['items' => $this->model->getItems(), 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/plain; charset=utf-8']],
            'sitemapindex' => ['content' => $this->view->make('sitemap::sitemapindex', ['sitemaps' => $this->model->getSitemaps(), 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/xml; charset=utf-8']],
            default => ['content' => $this->view->make('sitemap::'.$format, ['items' => $this->model->getItems(), 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/xml; charset=utf-8']],
        };
    }

    /**
     * Generate sitemap and store it to a file.
     *
     * @throws InvalidArgumentException
     */
    public function store(string $format = 'xml', string $filename = 'sitemap', ?string $path = null, ?string $style = null): void
    {
        // turn off caching for this method
        $this->model->setUseCache(false);

        // use correct file extension
        (in_array($format, ['txt', 'html'], true)) ? $fe = $format : $fe = 'xml';

        if ($this->model->getUseGzip()) {
            $fe .= '.gz';
        }

        // use custom size limit for sitemaps
        if ($this->model->getMaxSize() > 0 && count($this->model->getItems()) > $this->model->getMaxSize()) {
            if ($this->model->getUseLimitSize()) {
                // limit size
                $this->model->limitSize($this->model->getMaxSize());
                $data = $this->generate($format);
            } else {
                // use sitemapindex and generate partial sitemaps
                foreach (array_chunk($this->model->getItems(), $this->model->getMaxSize()) as $key => $item) {
                    // reset current items
                    $this->model->resetItems($item);

                    // generate new partial sitemap
                    $this->store($format, $filename.'-'.$key, $path, $style);

                    // add sitemap to sitemapindex
                    if ($path !== null) {
                        // if using custom path generate relative urls for sitemaps in the sitemapindex
                        $this->addSitemap($filename.'-'.$key.'.'.$fe);
                    } else {
                        // else generate full urls based on app's domain
                        $this->addSitemap(url($filename.'-'.$key.'.'.$fe));
                    }
                }

                $data = $this->generate('sitemapindex');
            }
        } elseif (($format !== 'google-news' && count($this->model->getItems()) > 50000)
            || ($format === 'google-news' && count($this->model->getItems()) > 1000)) {
            ($format !== 'google-news') ? $max = 50000 : $max = 1000;

            // check if limiting size of items array is enabled
            if (! $this->model->getUseLimitSize()) {
                // use sitemapindex and generate partial sitemaps
                foreach (array_chunk($this->model->getItems(), $max) as $key => $item) {
                    // reset current items
                    $this->model->resetItems($item);

                    // generate new partial sitemap
                    $this->store($format, $filename.'-'.$key, $path, $style);

                    // add sitemap to sitemapindex
                    if ($path !== null) {
                        // if using custom path generate relative urls for sitemaps in the sitemapindex
                        $this->addSitemap($filename.'-'.$key.'.'.$fe);
                    } else {
                        // else generate full urls based on app's domain
                        $this->addSitemap(url($filename.'-'.$key.'.'.$fe));
                    }
                }

                $data = $this->generate('sitemapindex');
            } else {
                // reset items and use only most recent $max items
                $this->model->limitSize($max);
                $data = $this->generate($format);
            }
        } else {
            $data = $this->generate($format);
        }

        // clear memory
        if ($format === 'sitemapindex') {
            $this->model->resetSitemaps();
        }

        $this->model->resetItems();

        // if custom path
        if ($path === null) {
            $file = public_path(DIRECTORY_SEPARATOR.$filename.'.'.$fe);
        } else {
            $file = $path.DIRECTORY_SEPARATOR.$filename.'.'.$fe;
        }

        if ($this->model->getUseGzip()) {
            // write file (gzip compressed)
            $encode = gzencode($data['content'], 9);
            if (is_string($encode)) {
                $this->file->put($file, $encode);
            }
        } else {
            // write file
            $this->file->put($file, $data['content']);
        }
    }
}
