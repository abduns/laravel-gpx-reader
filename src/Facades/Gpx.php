<?php

namespace Dunn\GpxReader\Facades;

use Dunn\GpxReader\GpxParser;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Dunn\GpxReader\DTO\GpxDocument parseFromString(string $xml)
 * @method static \Dunn\GpxReader\DTO\GpxDocument parseFromFile(string $path)
 * 
 * @see \Dunn\GpxReader\GpxParser
 */
class Gpx extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GpxParser::class;
    }
}
