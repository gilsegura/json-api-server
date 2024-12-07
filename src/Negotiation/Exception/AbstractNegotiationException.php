<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Exception;

abstract class AbstractNegotiationException extends \Exception implements NegotiationExceptionInterface
{
    #[\Override]
    public function meta(): array
    {
        return [];
    }
}
