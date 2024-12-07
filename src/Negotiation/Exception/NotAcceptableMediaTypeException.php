<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Exception;

use JsonApi\Server\Definition\Error\Error;
use JsonApi\Server\Definition\Source\Source;

final class NotAcceptableMediaTypeException extends AbstractNegotiationException
{
    public const string ERROR_CODE = 'not_acceptable_media_type';

    private function __construct()
    {
        parent::__construct('The provided media type does not accepted.', 406);
    }

    public static function notAcceptable(): self
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
                'No acceptable media type',
                $this->message
            )
                ->withSource(
                    Source::header('accept')
                ),
        ];
    }
}
