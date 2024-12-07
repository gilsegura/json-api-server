<?php

declare(strict_types=1);

namespace JsonApi\Server\Request;

use Serializer\SerializableInterface;

final readonly class SortsCollection implements SerializableInterface
{
    /** @var Sort[] */
    public array $sorts;

    public function __construct(Sort ...$sorts)
    {
        $this->sorts = $sorts;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(
            static fn (string $attribute, string $order): Sort => Sort::{$order}($attribute),
            array_keys($data), $data
        ));
    }

    #[\Override]
    public function serialize(): array
    {
        return [...call_user_func(
            static function (Sort ...$sorts): \Generator {
                foreach ($sorts as $sort) {
                    yield $sort->attribute => $sort->order;
                }
            },
            ...$this->sorts
        )];
    }
}
