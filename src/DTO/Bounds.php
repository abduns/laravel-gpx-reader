<?php

namespace Dunn\GpxReader\DTO;

class Bounds
{
    public function __construct(
        public float $minlat,
        public float $minlon,
        public float $maxlat,
        public float $maxlon,
    ) {}
}
