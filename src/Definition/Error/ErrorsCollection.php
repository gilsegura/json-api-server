<?php

declare(strict_types=1);

namespace JsonApi\Server\Definition\Error;

use Serializer\SerializableInterface;

final readonly class ErrorsCollection implements SerializableInterface
{
    /** @var Error[] */
    public array $errors;

    public function __construct(Error ...$errors)
    {
        $this->errors = $errors;
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self(...array_map(static fn (array $error) => Error::deserialize($error), $data));
    }

    #[\Override]
    public function serialize(): array
    {
        return array_map(static fn (Error $errors): array => $errors->serialize(), $this->errors);
    }
}
