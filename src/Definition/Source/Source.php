<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Source;

use ProxyAssert\Assertion;

final readonly class Source
{
    private const string POINTER = 'pointer';

    private const string PARAMETER = 'parameter';

    private const string HEADER = 'header';

    public string $name;

    private function __construct(
        string $name,
        public string $value,
    ) {
        Assertion::inArray($name, [self::POINTER, self::PARAMETER, self::HEADER]);

        $this->name = $name;
    }

    public static function pointer(string $pointer): self
    {
        return new self(
            self::POINTER,
            $pointer
        );
    }

    public static function parameter(string $parameter): self
    {
        return new self(
            self::PARAMETER,
            $parameter
        );
    }

    public static function header(string $header): self
    {
        return new self(
            self::HEADER,
            $header
        );
    }
}
