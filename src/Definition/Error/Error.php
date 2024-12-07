<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Error;

use JsonApi\Server\Definition\Link\Link;
use JsonApi\Server\Definition\Link\LinksCollection;
use JsonApi\Server\Definition\Source\Source;
use JsonApi\Server\Definition\Source\SourcesCollection;
use Serializer\SerializableInterface;

final readonly class Error implements SerializableInterface
{
    private function __construct(
        public string $status,
        public string $code,
        public string $title,
        public string $detail,
        public ?SourcesCollection $source = null,
        public ?LinksCollection $links = null,
    ) {
    }

    public static function error(
        string $status,
        string $code,
        string $title,
        string $detail,
    ): self {
        return new self(
            $status,
            $code,
            $title,
            $detail
        );
    }

    public function withSource(Source $source): self
    {
        return new self(
            $this->status,
            $this->code,
            $this->title,
            $this->detail,
            new SourcesCollection($source),
            $this->links
        );
    }

    public function withLink(Link $link): self
    {
        return new self(
            $this->status,
            $this->code,
            $this->title,
            $this->detail,
            $this->source,
            new LinksCollection($link)
        );
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(
            $data['status'],
            $data['code'],
            $data['title'],
            $data['detail'],
            isset($data['source']) ? SourcesCollection::deserialize($data['source']) : null,
            isset($data['links']) ? LinksCollection::deserialize($data['links']) : null,
        );
    }

    #[\Override]
    public function serialize(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'title' => $this->title,
            'detail' => $this->detail,
            ...$this->source instanceof SourcesCollection ? ['source' => $this->source->serialize()] : [],
            ...$this->links instanceof LinksCollection ? ['links' => $this->links->serialize()] : [],
        ];
    }
}
