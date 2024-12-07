<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition;

use JsonApi\Server\Definition\Error\Error;
use JsonApi\Server\Definition\Error\ErrorsCollection;
use JsonApi\Server\Definition\Link\Link;
use JsonApi\Server\Definition\Link\LinksCollection;
use JsonApi\Server\Definition\Meta\Meta;
use JsonApi\Server\Definition\Meta\MetaCollection;

final readonly class ErrorDocument implements DocumentInterface
{
    public function __construct(
        public ErrorsCollection $errors,
        public ?MetaCollection $meta = null,
        public ?MetaCollection $jsonapi = null,
        public ?LinksCollection $links = null,
    ) {
    }

    public static function document(Error ...$errors): self
    {
        return new self(
            new ErrorsCollection(...$errors)
        );
    }

    public function withMeta(Meta ...$meta): self
    {
        return new self(
            $this->errors,
            new MetaCollection(...$meta),
            $this->jsonapi,
            $this->links
        );
    }

    public function withJsonapi(Meta ...$jsonapi): self
    {
        return new self(
            $this->errors,
            $this->meta,
            new MetaCollection(...$jsonapi),
            $this->links
        );
    }

    public function withLinks(Link ...$links): self
    {
        return new self(
            $this->errors,
            $this->meta,
            $this->jsonapi,
            new LinksCollection(...$links)
        );
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(
            ErrorsCollection::deserialize($data['errors']),
            isset($data['meta']) ? MetaCollection::deserialize($data['meta']) : null,
            isset($data['jsonapi']) ? MetaCollection::deserialize($data['jsonapi']) : null,
            isset($data['links']) ? LinksCollection::deserialize($data['links']) : null,
        );
    }

    #[\Override]
    public function serialize(): array
    {
        return [
            ...$this->meta instanceof MetaCollection ? ['meta' => $this->meta->serialize()] : [],
            ...$this->jsonapi instanceof MetaCollection ? ['jsonapi' => $this->jsonapi->serialize()] : [],
            ...$this->links instanceof LinksCollection ? ['links' => $this->links->serialize()] : [],
            'errors' => $this->errors->serialize(),
        ];
    }
}
