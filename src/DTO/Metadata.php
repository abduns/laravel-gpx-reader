<?php

namespace Dunn\GpxReader\DTO;

use DateTimeImmutable;

class Metadata
{
    /**
     * @param Link[] $links
     * @param string[] $keywords
     */
    public function __construct(
        public ?string $name = null,
        public ?string $desc = null,
        public ?Person $author = null,
        public ?Copyright $copyright = null,
        public array $links = [],
        public ?DateTimeImmutable $time = null,
        public ?string $keywords = null,
        public ?Bounds $bounds = null,
        public mixed $extensions = null,
    ) {}
}
