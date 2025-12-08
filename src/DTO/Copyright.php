<?php

namespace Dunn\GpxReader\DTO;

class Copyright
{
    public function __construct(
        public string $author,
        public ?string $year = null,
        public ?string $license = null,
    ) {}
}
