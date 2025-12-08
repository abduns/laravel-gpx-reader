<?php

use Dunn\GpxReader\DTO\GpxDocument;
use Dunn\GpxReader\Exceptions\InvalidGpxException;
use Dunn\GpxReader\Exceptions\GpxParseException;
use Dunn\GpxReader\Facades\Gpx;
use Dunn\GpxReader\GpxParser;

it('can parse a minimal valid GPX file', function () {
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gpx version="1.1" creator="TestApp">
</gpx>
XML;

    $gpx = Gpx::parseFromString($xml);

    expect($gpx)->toBeInstanceOf(GpxDocument::class)
        ->and($gpx->version)->toBe('1.1')
        ->and($gpx->creator)->toBe('TestApp');
});

it('can parse metadata', function () {
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gpx version="1.1" creator="TestApp">
  <metadata>
    <name>Test GPX</name>
    <desc>Description</desc>
    <author>
      <name>John Doe</name>
      <email id="john" domain="example.com"/>
    </author>
    <time>2023-10-27T10:00:00Z</time>
  </metadata>
</gpx>
XML;

    $gpx = Gpx::parseFromString($xml);

    expect($gpx->metadata->name)->toBe('Test GPX')
        ->and($gpx->metadata->desc)->toBe('Description')
        ->and($gpx->metadata->author->name)->toBe('John Doe')
        ->and($gpx->metadata->author->email->toString())->toBe('john@example.com')
        ->and($gpx->metadata->time->format('Y-m-d H:i:s'))->toBe('2023-10-27 10:00:00');
});

it('can parse waypoints', function () {
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gpx version="1.1" creator="TestApp">
  <wpt lat="48.8566" lon="2.3522">
    <ele>35.0</ele>
    <name>Paris</name>
  </wpt>
  <wpt lat="51.5074" lon="-0.1278">
    <name>London</name>
  </wpt>
</gpx>
XML;

    $gpx = Gpx::parseFromString($xml);

    expect($gpx->waypoints)->toHaveCount(2)
        ->and($gpx->waypoints[0]->latitude)->toBe(48.8566)
        ->and($gpx->waypoints[0]->longitude)->toBe(2.3522)
        ->and($gpx->waypoints[0]->elevation)->toBe(35.0)
        ->and($gpx->waypoints[0]->name)->toBe('Paris')
        ->and($gpx->waypoints[1]->name)->toBe('London');
});

it('can parse routes', function () {
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gpx version="1.1" creator="TestApp">
  <rte>
    <name>Route 1</name>
    <rtept lat="48.8566" lon="2.3522">
      <name>Start</name>
    </rtept>
    <rtept lat="51.5074" lon="-0.1278">
      <name>End</name>
    </rtept>
  </rte>
</gpx>
XML;

    $gpx = Gpx::parseFromString($xml);

    expect($gpx->routes)->toHaveCount(1)
        ->and($gpx->routes[0]->name)->toBe('Route 1')
        ->and($gpx->routes[0]->points)->toHaveCount(2)
        ->and($gpx->routes[0]->points[0]->name)->toBe('Start');
});

it('can parse tracks with segments', function () {
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gpx version="1.1" creator="TestApp">
  <trk>
    <name>Track 1</name>
    <trkseg>
      <trkpt lat="48.8566" lon="2.3522">
        <ele>10</ele>
      </trkpt>
      <trkpt lat="48.8570" lon="2.3530">
        <ele>12</ele>
      </trkpt>
    </trkseg>
    <trkseg>
       <trkpt lat="50.0" lon="3.0"></trkpt>
    </trkseg>
  </trk>
</gpx>
XML;

    $gpx = Gpx::parseFromString($xml);

    expect($gpx->tracks)->toHaveCount(1)
        ->and($gpx->tracks[0]->name)->toBe('Track 1')
        ->and($gpx->tracks[0]->segments)->toHaveCount(2)
        ->and($gpx->tracks[0]->segments[0]->points)->toHaveCount(2)
        ->and($gpx->tracks[0]->segments[0]->points[0]->elevation)->toBe(10.0)
        ->and($gpx->tracks[0]->segments[1]->points)->toHaveCount(1);
});

it('throws exception for invalid XML', function () {
    $xml = 'invalid xml';
    Gpx::parseFromString($xml);
})->throws(GpxParseException::class);

it('throws exception for missing gpx root', function () {
    $xml = '<notgpx></notgpx>';
    Gpx::parseFromString($xml);
})->throws(InvalidGpxException::class, "Root element must be 'gpx'");

it('throws exception for unsupported version in strict mode', function () {
    $xml = '<gpx version="1.0" creator="TestApp"></gpx>';
    Gpx::parseFromString($xml);
})->throws(InvalidGpxException::class, "Unsupported GPX version");

it('can parse extensions', function () {
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gpx version="1.1" creator="TestApp" xmlns:ex="http://example.com/ns">
  <extensions>
    <ex:custom>Value</ex:custom>
  </extensions>
</gpx>
XML;

    $gpx = Gpx::parseFromString($xml);

    expect($gpx->extensions)->not->toBeNull();
    // SimpleXMLElement access
    // Note: Namespaced children in SimpleXML need handling.
    // But our parser returns the SimpleXMLElement for extensions.
    // Let's check if we can access it.
    // Since we didn't register namespace in the test, we might need to be careful.
    // But the parser just returns the node.
});

it('can parse from file', function () {
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gpx version="1.1" creator="TestApp">
</gpx>
XML;
    $path = __DIR__ . '/test.gpx';
    file_put_contents($path, $xml);

    $gpx = Gpx::parseFromFile($path);

    expect($gpx)->toBeInstanceOf(GpxDocument::class);

    unlink($path);
});
