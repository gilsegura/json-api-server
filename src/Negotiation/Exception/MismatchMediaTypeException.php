<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Exception;

use JsonApi\Server\Definition\Error\Error;
use JsonApi\Server\Definition\Source\Source;

final class MismatchMediaTypeException extends AbstractNegotiationException
{
    public const string ERROR_CODE = 'mismatch_media_type';

    private function __construct()
    {
        parent::__construct('The provided body does not match media-type.', 500);
    }

    public static function mismatch(): self
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
                'Mismatch media type',
                $this->message
            )
                ->withSource(
                    Source::parameter('body')
                ),
        ];
    }
}
