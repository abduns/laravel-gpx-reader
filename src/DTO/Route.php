<?php

namespace Dunn\GpxReader\DTO;

class Route
{
    /**
     * @param Link[] $links
     * @param RoutePoint[] $points
     */
    public function __construct(
        public ?string $name = null,
        public ?string $cmt = null,
        public ?string $desc = null,
        public ?string $src = null,
        public array $links = [],
        public ?int $number = null,
        public ?string $type = null,
        public mixed $extensions = null,
        public array $points = [],
    ) {}
}
