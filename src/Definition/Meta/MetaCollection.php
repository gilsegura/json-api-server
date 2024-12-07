<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Meta;

use Serializer\SerializableInterface;

final readonly class MetaCollection implements SerializableInterface
{
    /** @var Meta[] */
    public array $meta;

    public function __construct(Meta ...$meta)
    {
        $this->meta = $meta;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(
            static fn (string $name, mixed $value): Meta => Meta::kv($name, $value),
            array_keys($data), $data
        ));
    }

    #[\Override]
    public function serialize(): array
    {
        return [...call_user_func(
            static function (Meta ...$metas): \Generator {
                foreach ($metas as $meta) {
                    yield $meta->name => $meta->value;
                }
            },
            ...$this->meta
        )];
    }
}
