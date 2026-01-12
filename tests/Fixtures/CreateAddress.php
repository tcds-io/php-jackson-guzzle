<?php

namespace Test\Tcds\Io\Jackson\Guzzle\Fixtures;

readonly class CreateAddress
{
    public function __construct(
        public string $street,
        public int $number,
    ) {
    }
}
