<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Query;

use JsonApi\Server\Negotiation\Exception\MalformedQueryParamException;
use JsonApi\Server\Request\Page;
use ProxyAssert\Exception\AssertionFailedException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Validator\MessageValidatorInterface;

final readonly class PageValidator implements MessageValidatorInterface
{
    #[\Override]
    public function __invoke(MessageInterface $message): MessageInterface
    {
        if (!$message instanceof RequestInterface) {
            return $message;
        }

        parse_str($message->getUri()->getQuery(), $parameters);

        if (!array_key_exists('page', $parameters)) {
            return $message;
        }

        if (!is_array($parameters['page'])) {
            throw MalformedQueryParamException::malformed('page');
        }

        if (array_filter($val = array_values($parameters['page']), 'is_string') !== $val) {
            throw MalformedQueryParamException::malformed('page');
        }

        if ([] !== array_filter($page = filter_var_array($parameters['page'], FILTER_VALIDATE_INT), 'is_bool')) {
            throw MalformedQueryParamException::malformed('page');
        }

        try {
            $page = Page::deserialize($page);
        } catch (AssertionFailedException) {
            throw MalformedQueryParamException::malformed('page');
        }

        if (!$message instanceof ServerRequestInterface) {
            return $message;
        }

        return $message->withAttribute(
            $page::class,
            $page
        );
    }
}
