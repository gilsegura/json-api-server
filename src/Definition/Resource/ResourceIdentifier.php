<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Resource;

use Serializer\SerializableInterface;

final readonly class ResourceIdentifier implements ResourceIdentifierInterface, SerializableInterface
{
    public function __construct(
        private string $id,
        private string $type,
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

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(
            $data['id'],
            $data['type'],
        );
    }

    #[\Override]
    public function serialize(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
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
}
