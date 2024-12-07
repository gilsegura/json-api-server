<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Meta;

use ProxyAssert\Assertion;

final readonly class Meta
{
    public string $name;

    private function __construct(
        string $name,
        public mixed $value,
    ) {
        Assertion::notEmpty($name);

        $this->name = $name;
    }

    public static function kv(
        string $name,
        mixed $value,
    ): self {
        return new self(
            $name,
            $value
        );
    }
}
