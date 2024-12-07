<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Resource;

use JsonApi\Server\Definition\Attribute\AttributesCollection;
use JsonApi\Server\Definition\Link\LinksCollection;
use JsonApi\Server\Definition\Meta\MetaCollection;
use JsonApi\Server\Definition\Relationship\RelationshipsCollection;

interface ResourceInterface
{
    public function id(): string;

    public function type(): string;

    public function attributes(): ?AttributesCollection;

    public function relationships(): ?RelationshipsCollection;

    public function links(): ?LinksCollection;

    public function meta(): ?MetaCollection;
}
