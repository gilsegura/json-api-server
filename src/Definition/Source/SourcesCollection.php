<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Source;

use Serializer\SerializableInterface;

final readonly class SourcesCollection implements SerializableInterface
{
    /** @var Source[] */
    public array $sources;

    public function __construct(Source ...$sources)
    {
        $this->sources = $sources;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(
            static fn (string $name, string $value): Source => Source::{$name}($value),
            array_keys($data), $data
        ));
    }

    #[\Override]
    public function serialize(): array
    {
        return [...call_user_func(
            static function (Source ...$sources): \Generator {
                foreach ($sources as $source) {
                    yield $source->name => $source->value;
                }
            },
            ...$this->sources
        )];
    }
}
