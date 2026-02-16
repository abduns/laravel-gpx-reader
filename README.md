# Laravel GPX Reader

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abduns/laravel-gpx-reader.svg?style=flat-square)](https://packagist.org/packages/abduns/laravel-gpx-reader)
[![License](https://img.shields.io/packagist/l/abduns/laravel-gpx-reader.svg?style=flat-square)](https://packagist.org/packages/abduns/laravel-gpx-reader)

A robust, dependency-free Laravel package to parse and work with GPX 1.1 files. Convert GPX XML into rich, type-safe PHP objects.

## Features

- 🚀 **GPX 1.1 Support**: Fully compliant with the GPX 1.1 schema.
- 📦 **No External Dependencies**: Uses native PHP XML parsing.
- 🛠 **Laravel Integration**: Includes Facade, Service Provider, and Config.
- 🛡 **Type-Safe DTOs**: Work with rich PHP objects (`Track`, `Route`, `Waypoint`) instead of raw arrays or XML.
- ✅ **Validation**: Optional strict mode to ensure GPX validity.

## Installation

You can install the package via composer:

```bash
composer require abduns/laravel-gpx-reader
```

## Usage

### Parsing a GPX file

You can parse a GPX file from a path or a string using the `Gpx` facade.

```php
use Dunn\GpxReader\Facades\Gpx;

// From file
$gpx = Gpx::parseFromFile('path/to/file.gpx');

// From string
$gpx = Gpx::parseFromString($xmlString);
```

### Working with the GPX Document

The parser returns a `Dunn\GpxReader\DTO\GpxDocument` object, which mirrors the GPX 1.1 schema.

```php
// Access metadata
echo $gpx->creator;
echo $gpx->version;
if ($gpx->metadata) {
    echo $gpx->metadata->name;
    echo $gpx->metadata->desc;
    echo $gpx->metadata->time?->format('Y-m-d H:i:s');
}

// Access Waypoints
foreach ($gpx->waypoints as $waypoint) {
    echo "Waypoint: {$waypoint->name} ({$waypoint->latitude}, {$waypoint->longitude})";
    echo " - Elevation: {$waypoint->elevation}"; // Access elevation
}

// Access Routes
foreach ($gpx->routes as $route) {
    echo "Route: {$route->name}";
    foreach ($route->points as $point) {
        echo " - Point: {$point->latitude}, {$point->longitude}";
        echo " - Elevation: {$point->elevation}"; // Access elevation
    }
}

// Access Tracks
foreach ($gpx->tracks as $track) {
    echo "Track: {$track->name}";
    foreach ($track->segments as $segment) {
        foreach ($segment->points as $point) {
            echo " - Point: {$point->latitude}, {$point->longitude}";
            echo " - Elevation: {$point->elevation}"; // Access elevation
        }
    }
}
```

### Configuration

You can publish the config file with:

```bash
php artisan vendor:publish --tag="gpx-config"
```

The config file allows you to configure strict mode and timezone.

```php
return [
    'strict_mode' => true, // Throw exceptions for invalid GPX structure
    'timezone' => 'UTC',
];
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
