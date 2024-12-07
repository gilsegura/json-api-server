<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Query;

use JsonApi\Server\Negotiation\Exception\MalformedQueryParamException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Validator\MessageValidatorInterface;

final readonly class IncludeValidator implements MessageValidatorInterface
{
    #[\Override]
    public function __invoke(MessageInterface $message): MessageInterface
    {
        if (!$message instanceof RequestInterface) {
            return $message;
        }

        parse_str($message->getUri()->getQuery(), $parameters);

        if (!array_key_exists('include', $parameters)) {
            return $message;
        }

        if (!is_string($parameters['include'])) {
            throw MalformedQueryParamException::malformed('include');
        }

        if (!preg_match('#^[a-z_]+(?:\.[a-z_]+)*$#', $parameters['include'])) {
            throw MalformedQueryParamException::malformed('include');
        }

        return $message;
    }
}
