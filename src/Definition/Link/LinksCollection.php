<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Link;

use Serializer\SerializableInterface;

final readonly class LinksCollection implements SerializableInterface
{
    /** @var Link[] */
    public array $links;

    public function __construct(Link ...$links)
    {
        $this->links = $links;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(
            static fn (string $name, mixed $href): Link => Link::{$name}($href),
            array_keys($data), $data
        ));
    }

    #[\Override]
    public function serialize(): array
    {
        return [...call_user_func(
            static function (Link ...$links): \Generator {
                foreach ($links as $link) {
                    yield $link->name => $link->href;
                }
            },
            ...$this->links
        )];
    }
}
