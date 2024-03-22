<?php

namespace TahsinGokalp\Sitemap;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SitemapServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sitemap');

        $config_file = __DIR__.'/../config/config.php';

        $this->mergeConfigFrom($config_file, 'sitemap');

        $this->publishes([
            $config_file => config_path('sitemap.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/sitemap'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/sitemap'),
        ], 'public');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind('sitemap', function (Container $app) {
            $config = $app->make('config');

            return new Sitemap(
                (array) $config->get('sitemap'),
                $app->make('cache.store'),
                $config,
                $app->make('files'),
                $app->make(ResponseFactory::class),
                $app->make('view')
            );
        });

        $this->app->alias('sitemap', Sitemap::class);
    }

    /**
     * {@inheritdoc}
     */
    public function provides(): array
    {
        return ['sitemap', Sitemap::class];
    }
}
