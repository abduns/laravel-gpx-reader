<?php

namespace Dunn\GpxReader\DTO;

class Link
{
    public function __construct(
        public string $href,
        public ?string $text = null,
        public ?string $type = null,
    ) {}
}
