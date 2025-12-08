<?php

namespace Dunn\GpxReader;

use Dunn\GpxReader\DTO\Bounds;
use Dunn\GpxReader\DTO\Copyright;
use Dunn\GpxReader\DTO\Email;
use Dunn\GpxReader\DTO\GpxDocument;
use Dunn\GpxReader\DTO\Link;
use Dunn\GpxReader\DTO\Metadata;
use Dunn\GpxReader\DTO\Person;
use Dunn\GpxReader\DTO\Point;
use Dunn\GpxReader\DTO\Route;
use Dunn\GpxReader\DTO\RoutePoint;
use Dunn\GpxReader\DTO\Track;
use Dunn\GpxReader\DTO\TrackPoint;
use Dunn\GpxReader\DTO\TrackSegment;
use Dunn\GpxReader\DTO\Waypoint;
use Dunn\GpxReader\Exceptions\GpxParseException;
use Dunn\GpxReader\Exceptions\InvalidGpxException;
use SimpleXMLElement;
use DateTimeImmutable;
use Exception;

class GpxParser
{
    protected bool $strictMode = true;

    public function __construct(bool $strictMode = true)
    {
        $this->strictMode = $strictMode;
    }

    public function parseFromFile(string $path): GpxDocument
    {
        if (!file_exists($path)) {
            throw new GpxParseException("File not found: {$path}");
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new GpxParseException("Unable to read file: {$path}");
        }

        return $this->parseFromString($content);
    }

    public function parseFromString(string $xml): GpxDocument
    {
        // Suppress warnings for invalid XML if we want to handle them manually, 
        // but libxml_use_internal_errors(true) is better.
        $previousState = libxml_use_internal_errors(true);

        try {
            $xmlElement = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new GpxParseException("Invalid XML: " . $e->getMessage());
        } finally {
            libxml_use_internal_errors($previousState);
        }

        // Register namespace
        $namespaces = $xmlElement->getNamespaces(true);
        // If default namespace is not set or not GPX 1.1, we might have issues if strict.
        // But SimpleXML handles default namespace automatically if we don't specify prefix in xpath, 
        // or we can just access properties directly.
        
        // Validate root element
        if ($xmlElement->getName() !== 'gpx') {
            throw new InvalidGpxException("Root element must be 'gpx'");
        }

        $version = (string) $xmlElement['version'];
        $creator = (string) $xmlElement['creator'];

        if ($this->strictMode) {
            if ($version !== '1.1') {
                throw new InvalidGpxException("Unsupported GPX version: {$version}. Only 1.1 is supported.");
            }
            if (empty($creator)) {
                throw new InvalidGpxException("Missing 'creator' attribute.");
            }
        }

        $metadata = isset($xmlElement->metadata) ? $this->parseMetadata($xmlElement->metadata) : null;

        $waypoints = [];
        foreach ($xmlElement->wpt as $wpt) {
            $waypoints[] = $this->parsePoint($wpt, Waypoint::class);
        }

        $routes = [];
        foreach ($xmlElement->rte as $rte) {
            $routes[] = $this->parseRoute($rte);
        }

        $tracks = [];
        foreach ($xmlElement->trk as $trk) {
            $tracks[] = $this->parseTrack($trk);
        }

        $extensions = isset($xmlElement->extensions) ? $this->parseExtensions($xmlElement->extensions) : null;

        return new GpxDocument(
            version: $version,
            creator: $creator,
            metadata: $metadata,
            waypoints: $waypoints,
            routes: $routes,
            tracks: $tracks,
            extensions: $extensions
        );
    }

    protected function parseMetadata(SimpleXMLElement $xml): Metadata
    {
        return new Metadata(
            name: $this->getString($xml->name),
            desc: $this->getString($xml->desc),
            author: isset($xml->author) ? $this->parsePerson($xml->author) : null,
            copyright: isset($xml->copyright) ? $this->parseCopyright($xml->copyright) : null,
            links: $this->parseLinks($xml->link),
            time: $this->getDate($xml->time),
            keywords: $this->getString($xml->keywords),
            bounds: isset($xml->bounds) ? $this->parseBounds($xml->bounds) : null,
            extensions: isset($xml->extensions) ? $this->parseExtensions($xml->extensions) : null,
        );
    }

    protected function parsePerson(SimpleXMLElement $xml): Person
    {
        return new Person(
            name: $this->getString($xml->name),
            email: isset($xml->email) ? $this->parseEmail($xml->email) : null,
            link: isset($xml->link) ? $this->parseLink($xml->link) : null,
        );
    }

    protected function parseEmail(SimpleXMLElement $xml): Email
    {
        return new Email(
            id: (string) $xml['id'],
            domain: (string) $xml['domain'],
        );
    }

    protected function parseLink(SimpleXMLElement $xml): Link
    {
        return new Link(
            href: (string) $xml['href'],
            text: $this->getString($xml->text),
            type: $this->getString($xml->type),
        );
    }

    protected function parseLinks(SimpleXMLElement $xml = null): array
    {
        $links = [];
        if ($xml) {
            // SimpleXML iteration over same-named elements
            foreach ($xml as $link) {
                $links[] = $this->parseLink($link);
            }
        }
        return $links;
    }

    protected function parseCopyright(SimpleXMLElement $xml): Copyright
    {
        return new Copyright(
            author: (string) $xml['author'],
            year: $this->getString($xml->year),
            license: $this->getString($xml->license),
        );
    }

    protected function parseBounds(SimpleXMLElement $xml): Bounds
    {
        return new Bounds(
            minlat: (float) $xml['minlat'],
            minlon: (float) $xml['minlon'],
            maxlat: (float) $xml['maxlat'],
            maxlon: (float) $xml['maxlon'],
        );
    }

    /**
     * @template T of Point
     * @param SimpleXMLElement $xml
     * @param class-string<T> $class
     * @return T
     */
    protected function parsePoint(SimpleXMLElement $xml, string $class): Point
    {
        return new $class(
            latitude: (float) $xml['lat'],
            longitude: (float) $xml['lon'],
            elevation: isset($xml->ele) ? (float) $xml->ele : null,
            time: $this->getDate($xml->time),
            magvar: isset($xml->magvar) ? (float) $xml->magvar : null,
            geoidheight: isset($xml->geoidheight) ? (float) $xml->geoidheight : null,
            name: $this->getString($xml->name),
            cmt: $this->getString($xml->cmt),
            desc: $this->getString($xml->desc),
            src: $this->getString($xml->src),
            links: $this->parseLinks($xml->link),
            sym: $this->getString($xml->sym),
            type: $this->getString($xml->type),
            fix: $this->getString($xml->fix),
            sat: isset($xml->sat) ? (int) $xml->sat : null,
            hdop: isset($xml->hdop) ? (float) $xml->hdop : null,
            vdop: isset($xml->vdop) ? (float) $xml->vdop : null,
            pdop: isset($xml->pdop) ? (float) $xml->pdop : null,
            ageofdgpsdata: isset($xml->ageofdgpsdata) ? (float) $xml->ageofdgpsdata : null,
            dgpsid: $this->getString($xml->dgpsid),
            extensions: isset($xml->extensions) ? $this->parseExtensions($xml->extensions) : null,
        );
    }

    protected function parseRoute(SimpleXMLElement $xml): Route
    {
        $points = [];
        foreach ($xml->rtept as $pt) {
            $points[] = $this->parsePoint($pt, RoutePoint::class);
        }

        return new Route(
            name: $this->getString($xml->name),
            cmt: $this->getString($xml->cmt),
            desc: $this->getString($xml->desc),
            src: $this->getString($xml->src),
            links: $this->parseLinks($xml->link),
            number: isset($xml->number) ? (int) $xml->number : null,
            type: $this->getString($xml->type),
            extensions: isset($xml->extensions) ? $this->parseExtensions($xml->extensions) : null,
            points: $points,
        );
    }

    protected function parseTrack(SimpleXMLElement $xml): Track
    {
        $segments = [];
        foreach ($xml->trkseg as $seg) {
            $segments[] = $this->parseTrackSegment($seg);
        }

        return new Track(
            name: $this->getString($xml->name),
            cmt: $this->getString($xml->cmt),
            desc: $this->getString($xml->desc),
            src: $this->getString($xml->src),
            links: $this->parseLinks($xml->link),
            number: isset($xml->number) ? (int) $xml->number : null,
            type: $this->getString($xml->type),
            extensions: isset($xml->extensions) ? $this->parseExtensions($xml->extensions) : null,
            segments: $segments,
        );
    }

    protected function parseTrackSegment(SimpleXMLElement $xml): TrackSegment
    {
        $points = [];
        foreach ($xml->trkpt as $pt) {
            $points[] = $this->parsePoint($pt, TrackPoint::class);
        }

        return new TrackSegment(
            points: $points,
            extensions: isset($xml->extensions) ? $this->parseExtensions($xml->extensions) : null,
        );
    }

    protected function parseExtensions(SimpleXMLElement $xml): mixed
    {
        // For now, just return the SimpleXMLElement as is, or an array of children.
        // The prompt says "Store raw XML nodes for extensions" or "generic representation".
        // Returning the SimpleXMLElement allows the user to traverse it.
        return $xml;
    }

    protected function getString(?SimpleXMLElement $element): ?string
    {
        return $element ? (string) $element : null;
    }

    protected function getDate(?SimpleXMLElement $element): ?DateTimeImmutable
    {
        if (!$element) {
            return null;
        }
        try {
            return new DateTimeImmutable((string) $element);
        } catch (Exception $e) {
            if ($this->strictMode) {
                throw new GpxParseException("Invalid date format: " . (string) $element);
            }
            return null;
        }
    }
}
