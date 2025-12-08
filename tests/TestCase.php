<?php

namespace Dunn\GpxReader\Tests;

use Dunn\GpxReader\Providers\GpxServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            GpxServiceProvider::class,
        ];
    }
}
