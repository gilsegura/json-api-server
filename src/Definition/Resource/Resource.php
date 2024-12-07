<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Resource;

use JsonApi\Server\Definition\Attribute\Attribute;
use JsonApi\Server\Definition\Attribute\AttributesCollection;
use JsonApi\Server\Definition\Link\Link;
use JsonApi\Server\Definition\Link\LinksCollection;
use JsonApi\Server\Definition\Meta\Meta;
use JsonApi\Server\Definition\Meta\MetaCollection;
use JsonApi\Server\Definition\Relationship\RelationshipInterface;
use JsonApi\Server\Definition\Relationship\RelationshipsCollection;
use Serializer\SerializableInterface;

final readonly class Resource implements ResourceInterface, SerializableInterface
{
    public function __construct(
        private string $id,
        private string $type,
        private ?AttributesCollection $attributes = null,
        private ?RelationshipsCollection $relationships = null,
        private ?LinksCollection $links = null,
        private ?MetaCollection $meta = null,
    ) {
    }

    public static function resource(
        string $id,
        string $type,
    ): self {
        return new self(
            $id,
            $type,
        );
    }

    public function withAttributes(Attribute ...$attributes): self
    {
        return new self(
            $this->id,
            $this->type,
            new AttributesCollection(...$attributes),
            $this->relationships,
            $this->links,
            $this->meta
        );
    }

    public function withRelationships(RelationshipInterface ...$relationships): self
    {
        return new self(
            $this->id,
            $this->type,
            $this->attributes,
            new RelationshipsCollection(...$relationships),
            $this->links,
            $this->meta
        );
    }

    public function withLink(Link $links): self
    {
        return new self(
            $this->id,
            $this->type,
            $this->attributes,
            $this->relationships,
            new LinksCollection($links),
            $this->meta
        );
    }

    public function withMeta(Meta ...$meta): self
    {
        return new self(
            $this->id,
            $this->type,
            $this->attributes,
            $this->relationships,
            $this->links,
            new MetaCollection(...$meta)
        );
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(
            $data['id'],
            $data['type'],
            isset($data['attributes']) ? AttributesCollection::deserialize($data['attributes']) : null,
            isset($data['relationships']) ? RelationshipsCollection::deserialize($data['relationships']) : null,
            isset($data['links']) ? LinksCollection::deserialize($data['links']) : null,
            isset($data['meta']) ? MetaCollection::deserialize($data['meta']) : null,
        );
    }

    #[\Override]
    public function serialize(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            ...$this->attributes instanceof AttributesCollection ? ['attributes' => $this->attributes->serialize()] : [],
            ...$this->relationships instanceof RelationshipsCollection ? ['relationships' => $this->relationships->serialize()] : [],
            ...$this->links instanceof LinksCollection ? ['links' => $this->links->serialize()] : [],
            ...$this->meta instanceof MetaCollection ? ['meta' => $this->meta->serialize()] : [],
        ];
    }

    #[\Override]
    public function id(): string
    {
        return $this->id;
    }

    #[\Override]
    public function type(): string
    {
        return $this->type;
    }

    #[\Override]
    public function attributes(): ?AttributesCollection
    {
        return $this->attributes;
    }

    #[\Override]
    public function relationships(): ?RelationshipsCollection
    {
        return $this->relationships;
    }

    #[\Override]
    public function links(): ?LinksCollection
    {
        return $this->links;
    }

    #[\Override]
    public function meta(): ?MetaCollection
    {
        return $this->meta;
    }
}
