<?php

declare(strict_types=1);

namespace JsonApi\Server\Request;

use ProxyAssert\Assertion;

final readonly class Sort
{
    public const string ASC = 'asc';

    public const string DESC = 'desc';

    private const array SORT = [
        self::ASC,
        self::DESC,
    ];

    public string $attribute;

    public string $order;

    public function __construct(
        string $attribute,
        string $order,
    ) {
        Assertion::regex($attribute, '#[a-z_]+#');
        Assertion::inArray($order, self::SORT);

        $this->attribute = $attribute;
        $this->order = $order;
    }

    public static function asc(string $attribute): self
    {
        return new self($attribute, self::ASC);
    }

    public static function desc(string $attribute): self
    {
        return new self($attribute, self::DESC);
    }
}
