<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Resource;

use Serializer\SerializableInterface;

final readonly class ResourceIdentifiersCollection implements SerializableInterface
{
    /** @var ResourceIdentifier[] */
    private array $resources;

    public function __construct(ResourceIdentifier ...$resources)
    {
        $this->resources = $resources;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(static fn (array $resource) => ResourceIdentifier::deserialize($resource), $data));
    }

    #[\Override]
    public function serialize(): array
    {
        return array_map(static fn (ResourceIdentifier $resources): array => $resources->serialize(), $this->resources);
    }
}
