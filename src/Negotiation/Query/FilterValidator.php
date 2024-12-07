<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Query;

use JsonApi\Server\Negotiation\Exception\MalformedQueryParamException;
use JsonApi\Server\Request\FiltersCollection;
use ProxyAssert\Exception\AssertionFailedException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Validator\MessageValidatorInterface;

final readonly class FilterValidator implements MessageValidatorInterface
{
    #[\Override]
    public function __invoke(MessageInterface $message): MessageInterface
    {
        if (!$message instanceof RequestInterface) {
            return $message;
        }

        parse_str($message->getUri()->getQuery(), $parameters);

        if (!array_key_exists('filter', $parameters)) {
            return $message;
        }

        if (!is_array($parameters['filter'])) {
            throw MalformedQueryParamException::malformed('filter');
        }

        if (array_filter($val = array_values($parameters['filter']), 'is_string') !== $val) {
            throw MalformedQueryParamException::malformed('filter');
        }

        try {
            $filters = FiltersCollection::deserialize($parameters['filter']);
        } catch (AssertionFailedException) {
            throw MalformedQueryParamException::malformed('filter');
        }

        if (!$message instanceof ServerRequestInterface) {
            return $message;
        }

        return $message->withAttribute(
            $filters::class,
            $filters
        );
    }
}
