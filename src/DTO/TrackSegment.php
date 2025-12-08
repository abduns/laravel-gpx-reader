<?php

namespace Dunn\GpxReader\DTO;

class TrackSegment
{
    /**
     * @param TrackPoint[] $points
     */
    public function __construct(
        public array $points = [],
        public mixed $extensions = null,
    ) {}
}
