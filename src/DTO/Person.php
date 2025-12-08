<?php

namespace Dunn\GpxReader\DTO;

class Person
{
    public function __construct(
        public ?string $name = null,
        public ?Email $email = null,
        public ?Link $link = null,
    ) {}
}
