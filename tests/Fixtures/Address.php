<?php

namespace Test\Tcds\Io\Jackson\Guzzle\Fixtures;

readonly class Address
{
    public function __construct(
        public string $id,
        public string $street,
        public int $number,
        public bool $main,
    ) {
    }
}
