<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Relationship;

use Serializer\SerializableInterface;

final readonly class RelationshipsCollection implements SerializableInterface
{
    /** @var RelationshipInterface[] */
    public array $relationships;

    public function __construct(RelationshipInterface ...$relationships)
    {
        $this->relationships = $relationships;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(static function (string $name, array $relationship): RelationshipInterface {
            if (array_keys($relationship['data']) !== range(0, count($relationship['data']) - 1)) {
                return ToOneRelationship::deserialize([$name => $relationship]);
            }

            return ToManyRelationship::deserialize([$name => $relationship]);
        }, array_keys($data), $data));
    }

    #[\Override]
    public function serialize(): array
    {
        return array_merge_recursive(...array_map(static fn (RelationshipInterface $relationship): array => $relationship->serialize(), $this->relationships));
    }
}
