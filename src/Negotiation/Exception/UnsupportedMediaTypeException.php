<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Exception;

use JsonApi\Server\Definition\Error\Error;
use JsonApi\Server\Definition\Source\Source;

final class UnsupportedMediaTypeException extends AbstractNegotiationException
{
    public const string ERROR_CODE = 'unsupported_media_type';

    private function __construct()
    {
        parent::__construct('The provided media type does not supported.', 415);
    }

    public static function unsupported(): self
    {
        return new self();
    }

    #[\Override]
    public function errors(): array
    {
        return [
            Error::error(
                (string) $this->code,
                self::ERROR_CODE,
                'Unsupported media type',
                $this->message
            )
                ->withSource(
                    Source::header('content-type')
                ),
        ];
    }
}
