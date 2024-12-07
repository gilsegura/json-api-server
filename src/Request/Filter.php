<?php

declare(strict_types=1);

namespace JsonApi\Server\Request;

use ProxyAssert\Assertion;

final readonly class Filter
{
    public string $name;

    private function __construct(
        string $name,
        public string $value,
    ) {
        Assertion::regex($name, '#^[a-z:_]+(?:\.[a-z_]+)*$#');

        $this->name = $name;
    }

    public static function kv(
        string $name,
        string $value,
    ): self {
        return new self(
            $name,
            $value
        );
    }
}
