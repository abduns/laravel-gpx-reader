<?php

namespace Dunn\GpxReader\Providers;

use Dunn\GpxReader\GpxParser;
use Illuminate\Support\ServiceProvider;

class GpxServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/gpx.php', 'gpx');

        $this->app->bind(GpxParser::class, function ($app) {
            return new GpxParser(
                strictMode: config('gpx.strict_mode', true)
            );
        });

        $this->app->alias(GpxParser::class, 'gpx');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/gpx.php' => config_path('gpx.php'),
            ], 'gpx-config');
        }
    }
}
