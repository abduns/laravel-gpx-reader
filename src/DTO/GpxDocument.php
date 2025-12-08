<?php

namespace Dunn\GpxReader\DTO;

class GpxDocument
{
    /**
     * @param Waypoint[] $waypoints
     * @param Route[] $routes
     * @param Track[] $tracks
     */
    public function __construct(
        public string $version,
        public string $creator,
        public ?Metadata $metadata = null,
        public array $waypoints = [],
        public array $routes = [],
        public array $tracks = [],
        public mixed $extensions = null,
    ) {}
}
