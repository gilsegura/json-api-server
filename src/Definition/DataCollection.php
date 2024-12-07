<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition;

use Serializer\SerializableInterface;

final readonly class DataCollection implements SerializableInterface
{
    /** @var Data[] */
    public array $data;

    public function __construct(Data ...$data)
    {
        $this->data = $data;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(static fn (array $resource) => Data::deserialize($resource), $data));
    }

    #[\Override]
    public function serialize(): array
    {
        return array_map(static fn (Data $data): array => $data->serialize(), $this->data);
    }
}
