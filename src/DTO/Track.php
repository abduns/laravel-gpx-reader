<?php

namespace Dunn\GpxReader\DTO;

class Track
{
    /**
     * @param Link[] $links
     * @param TrackSegment[] $segments
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
        public array $segments = [],
    ) {}
}
