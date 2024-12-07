<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Exception;

use JsonApi\Server\Definition\Error\Error;
use JsonApi\Server\Definition\Source\Source;

final class MalformedQueryParamException extends AbstractNegotiationException
{
    public const string ERROR_CODE = 'malformed_query_param';

    private function __construct(
        private readonly string $param,
    ) {
        parent::__construct(sprintf('The provided query param "%s" does not valid.', $this->param), 400);
    }

    public static function malformed(string $param): self
    {
        return new self($param);
    }

    #[\Override]
    public function errors(): array
    {
        return [
            Error::error(
                (string) $this->code,
                self::ERROR_CODE,
                'Malformed query param',
                $this->message
            )
                ->withSource(
                    Source::parameter($this->param)
                ),
        ];
    }
}
