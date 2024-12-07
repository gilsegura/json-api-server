<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Exception;

use JsonApi\Server\Definition\Error\Error;

final class MethodNotAllowedException extends AbstractNegotiationException
{
    public const string ERROR_CODE = 'method_not_allowed';

    private function __construct(
        private readonly string $method,
    ) {
        parent::__construct(sprintf('The requested method "%s" does not allowed.', $this->method), 405);
    }

    public static function method(string $method): self
    {
        return new self($method);
    }

    #[\Override]
    public function errors(): array
    {
        return [
            Error::error(
                (string) $this->code,
                self::ERROR_CODE,
                'Method not allowed',
                $this->message
            ),
        ];
    }
}
