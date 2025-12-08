<?php

namespace Dunn\GpxReader\DTO;

class Email
{
    public function __construct(
        public string $id,
        public string $domain,
    ) {}

    public function toString(): string
    {
        return "{$this->id}@{$this->domain}";
    }
}
