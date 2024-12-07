<?php

declare(strict_types=1);

namespace JsonApi\Server\Request;

use Serializer\SerializableInterface;

final readonly class FiltersCollection implements SerializableInterface
{
    /** @var Filter[] */
    public array $filters;

    public function __construct(Filter ...$filters)
    {
        $this->filters = $filters;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(
            static fn (string $name, string $value): Filter => Filter::kv($name, $value),
            array_keys($data), $data
        ));
    }

    #[\Override]
    public function serialize(): array
    {
        return [...call_user_func(
            static function (Filter ...$filters): \Generator {
                foreach ($filters as $filter) {
                    yield $filter->name => $filter->value;
                }
            },
            ...$this->filters
        )];
    }
}
