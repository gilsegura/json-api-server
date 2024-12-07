<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Exception;

use JsonApi\Server\Definition\Error\Error;
use JsonApi\Server\Definition\Source\Source;

final class MalformedContentException extends AbstractNegotiationException
{
    public const string ERROR_CODE = 'malformed_content';

    private function __construct(
        private readonly array $errors,
    ) {
        parent::__construct('The provided content is malformed.', 400);
    }

    public static function malformed(array $errors): self
    {
        return new self($errors);
    }

    #[\Override]
    public function errors(): array
    {
        return array_map(function (array $error): Error {
            return Error::error(
                (string) $this->code,
                self::ERROR_CODE,
                'Malformed content',
                $error['message']
            )
                ->withSource(
                    Source::pointer($error['pointer'])
                );
        }, $this->errors);
    }
}
