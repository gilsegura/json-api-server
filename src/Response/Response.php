<?php

declare(strict_types=1);

namespace JsonApi\Server\Response;

use JsonApi\Server\Definition\ErrorDocument;
use JsonApi\Server\Definition\ToManyDocument;
use JsonApi\Server\Definition\ToOneDocument;
use Psr\Http\Message\ResponseInterface;
use Psr\Server\ResponseFactory\Header;
use Psr\Server\ResponseFactory\ResponseFactory;
use Psr\Server\ResponseFactory\ResponseFactoryException;
use Psr\Server\ResponseFactory\Status;

final readonly class Response
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @throws ResponseFactoryException
     */
    public static function ok(ToOneDocument|ToManyDocument $document): ResponseInterface
    {
        return (new ResponseFactory())->__invoke(
            Status::OK,
            [Header::kv('Content-Type', 'application/vnd.api+json')],
            $document
        );
    }

    /**
     * @throws ResponseFactoryException
     */
    public static function created(ToOneDocument $document): ResponseInterface
    {
        return (new ResponseFactory())->__invoke(
            Status::CREATED,
            [Header::kv('Content-Type', 'application/vnd.api+json')],
            $document
        );
    }

    /**
     * @throws ResponseFactoryException
     */
    public static function accepted(ToOneDocument $document): ResponseInterface
    {
        return (new ResponseFactory())->__invoke(
            Status::ACCEPTED,
            [Header::kv('Content-Type', 'application/vnd.api+json')],
            $document
        );
    }

    /**
     * @throws ResponseFactoryException
     */
    public static function noContent(): ResponseInterface
    {
        return (new ResponseFactory())->__invoke(
            Status::NO_CONTENT,
        );
    }

    /**
     * @throws ResponseFactoryException
     */
    public static function error(ErrorDocument $document, int $code = 500): ResponseInterface
    {
        return (new ResponseFactory())->__invoke(
            $code,
            [Header::kv('Content-Type', 'application/vnd.api+json')],
            $document
        );
    }
}
