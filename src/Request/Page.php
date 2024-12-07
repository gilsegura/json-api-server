<?php

declare(strict_types=1);

namespace JsonApi\Server\Request;

use ProxyAssert\Assertion;
use Serializer\SerializableInterface;

final readonly class Page implements SerializableInterface
{
    public function __construct(
        public int $offset,
        public int $limit,
    ) {
    }

    #[\Override]
    public static function deserialize(array $data): SerializableInterface
    {
        Assertion::keyExists($data, 'offset');
        Assertion::keyExists($data, 'limit');

        return new self(
            $data['offset'],
            $data['limit'],
        );
    }

    #[\Override]
    public function serialize(): array
    {
        return [
            'offset' => $this->offset,
            'limit' => $this->limit,
        ];
    }
}
