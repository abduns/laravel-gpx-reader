<?php

namespace Dunn\GpxReader\DTO;

use DateTimeImmutable;

class Point
{
    /**
     * @param Link[] $links
     */
    public function __construct(
        public float $latitude,
        public float $longitude,
        public ?float $elevation = null,
        public ?DateTimeImmutable $time = null,
        public ?float $magvar = null,
        public ?float $geoidheight = null,
        public ?string $name = null,
        public ?string $cmt = null,
        public ?string $desc = null,
        public ?string $src = null,
        public array $links = [],
        public ?string $sym = null,
        public ?string $type = null,
        public ?string $fix = null,
        public ?int $sat = null,
        public ?float $hdop = null,
        public ?float $vdop = null,
        public ?float $pdop = null,
        public ?float $ageofdgpsdata = null,
        public ?string $dgpsid = null,
        public mixed $extensions = null,
    ) {}
}
