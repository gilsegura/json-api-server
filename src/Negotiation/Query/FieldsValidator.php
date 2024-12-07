<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Query;

use JsonApi\Server\Negotiation\Exception\MalformedQueryParamException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Validator\MessageValidatorInterface;

final readonly class FieldsValidator implements MessageValidatorInterface
{
    #[\Override]
    public function __invoke(MessageInterface $message): MessageInterface
    {
        if (!$message instanceof RequestInterface) {
            return $message;
        }

        parse_str($message->getUri()->getQuery(), $parameters);

        if (!array_key_exists('fields', $parameters)) {
            return $message;
        }

        if (!is_array($parameters['fields'])) {
            throw MalformedQueryParamException::malformed('fields');
        }

        foreach ($parameters['fields'] as $key => $value) {
            if (!preg_match('#^[a-z_]+$#', $key)) {
                throw MalformedQueryParamException::malformed('fields');
            }

            if (
                !is_string($value)
                || !preg_match('#^[a-z_]+(?:,[a-z_]+)*$#', $value)
            ) {
                throw MalformedQueryParamException::malformed('fields');
            }
        }

        return $message;
    }
}
