<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Resource;

use Serializer\SerializableInterface;

final readonly class ResourcesCollection implements SerializableInterface
{
    /** @var \JsonApi\Server\Definition\Resource\Resource[] */
    private array $resources;

    public function __construct(Resource ...$resources)
    {
        $this->resources = $resources;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(static fn (array $resource) => Resource::deserialize($resource), $data));
    }

    #[\Override]
    public function serialize(): array
    {
        return array_map(static fn (Resource $resources): array => $resources->serialize(), $this->resources);
    }
}
