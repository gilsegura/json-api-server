<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Relationship;

use JsonApi\Server\Definition\Link\Link;
use JsonApi\Server\Definition\Link\LinksCollection;
use JsonApi\Server\Definition\Meta\Meta;
use JsonApi\Server\Definition\Meta\MetaCollection;
use JsonApi\Server\Definition\Resource\ResourceIdentifier;

final readonly class ToOneRelationship implements RelationshipInterface
{
    private function __construct(
        public string $name,
        public ResourceIdentifier $data,
        public ?LinksCollection $links = null,
        public ?MetaCollection $meta = null,
    ) {
    }

    public static function relationship(
        string $name,
        ResourceIdentifier $data,
    ): self {
        return new self(
            $name,
            $data
        );
    }

    public function withLinks(Link ...$links): self
    {
        return new self(
            $this->name,
            $this->data,
            new LinksCollection(...$links),
            $this->meta
        );
    }

    public function withMeta(Meta ...$meta): self
    {
        return new self(
            $this->name,
            $this->data,
            $this->links,
            new MetaCollection(...$meta)
        );
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(
            (string) key($data),
            ResourceIdentifier::deserialize($data[(string) key($data)]['data']),
            isset($data[(string) key($data)]['links']) ? LinksCollection::deserialize($data[(string) key($data)]['links']) : null,
            isset($data[(string) key($data)]['meta']) ? MetaCollection::deserialize($data[(string) key($data)]['meta']) : null,
        );
    }

    #[\Override]
    public function serialize(): array
    {
        return [
            $this->name => [
                ...$this->links instanceof LinksCollection ? ['links' => $this->links->serialize()] : [],
                'data' => $this->data->serialize(),
                ...$this->meta instanceof MetaCollection ? ['meta' => $this->meta->serialize()] : [],
            ],
        ];
    }
}
