# Laravel GPX Reader

A Laravel package to read and work with GPX 1.1 files.

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
}

// Access Routes
foreach ($gpx->routes as $route) {
    echo "Route: {$route->name}";
    foreach ($route->points as $point) {
        echo " - Point: {$point->latitude}, {$point->longitude}";
    }
}

// Access Tracks
foreach ($gpx->tracks as $track) {
    echo "Track: {$track->name}";
    foreach ($track->segments as $segment) {
        foreach ($segment->points as $point) {
            echo " - Point: {$point->latitude}, {$point->longitude}, Ele: {$point->elevation}";
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
