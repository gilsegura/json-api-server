<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Query;

use JsonApi\Server\Negotiation\Exception\MalformedQueryParamException;
use JsonApi\Server\Request\Sort;
use JsonApi\Server\Request\SortsCollection;
use ProxyAssert\Exception\AssertionFailedException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Validator\MessageValidatorInterface;

final readonly class SortValidator implements MessageValidatorInterface
{
    #[\Override]
    public function __invoke(MessageInterface $message): MessageInterface
    {
        if (!$message instanceof RequestInterface) {
            return $message;
        }

        parse_str($message->getUri()->getQuery(), $parameters);

        if (!array_key_exists('sort', $parameters)) {
            return $message;
        }

        if (!is_string($parameters['sort'])) {
            throw MalformedQueryParamException::malformed('sort');
        }

        if (!preg_match('#^-?[a-z_]+(?:,-?[a-z_]+)*$#', $parameters['sort'])) {
            throw MalformedQueryParamException::malformed('sort');
        }

        try {
            $explode = explode(',', $parameters['sort']);

            $sorts = SortsCollection::deserialize([
                ...call_user_func(static function (string $order, string ...$attributes): \Generator {
                    foreach ($attributes as $attribute) {
                        yield $attribute => $order;
                    }
                }, Sort::ASC, ...is_array($asc = preg_grep('#^[a-z_]+$#', $explode)) ? $asc : []),
                ...call_user_func(static function (string $order, string ...$attributes): \Generator {
                    foreach ($attributes as $attribute) {
                        yield substr($attribute, 1) => $order;
                    }
                }, Sort::DESC, ...is_array($desc = preg_grep('#^-[a-z_]+$#', $explode)) ? $desc : []),
            ]);
        } catch (AssertionFailedException) {
            throw MalformedQueryParamException::malformed('sort');
        }

        if (!$message instanceof ServerRequestInterface) {
            return $message;
        }

        return $message->withAttribute(
            $sorts::class,
            $sorts
        );
    }
}
