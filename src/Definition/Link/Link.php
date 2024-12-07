<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Link;

use ProxyAssert\Assertion;

final readonly class Link
{
    private const string SELF = 'self';

    private const string RELATED = 'related';

    private const string DESCRIBEDBY = 'describedby';

    private const string ABOUT = 'about';

    private const string FIRST = 'first';

    private const string LAST = 'last';

    private const string PREV = 'prev';

    private const string NEXT = 'next';

    public string $name;

    private function __construct(
        string $name,
        public string $href,
    ) {
        Assertion::inArray($name, [self::SELF, self::RELATED, self::DESCRIBEDBY, self::ABOUT, self::FIRST, self::LAST, self::PREV, self::NEXT]);

        $this->name = $name;
    }

    public static function self(string $href): self
    {
        return new self(self::SELF, $href);
    }

    public static function related(string $href): self
    {
        return new self(self::RELATED, $href);
    }

    public static function describedby(string $href): self
    {
        return new self(self::DESCRIBEDBY, $href);
    }

    public static function about(string $href): self
    {
        return new self(self::ABOUT, $href);
    }

    public static function first(string $href): self
    {
        return new self(self::FIRST, $href);
    }

    public static function last(string $href): self
    {
        return new self(self::LAST, $href);
    }

    public static function prev(string $href): self
    {
        return new self(self::PREV, $href);
    }

    public static function next(string $href): self
    {
        return new self(self::NEXT, $href);
    }
}
