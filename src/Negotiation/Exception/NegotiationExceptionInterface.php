<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Exception;

use JsonApi\Server\Definition\Error\Error;
use JsonApi\Server\Definition\Meta\Meta;
use Psr\Validator\Exception\ValidationExceptionInterface;

interface NegotiationExceptionInterface extends ValidationExceptionInterface
{
    /**
     * @return Error[]
     */
    public function errors(): array;

    /**
     * @return Meta[]
     */
    public function meta(): array;
}
