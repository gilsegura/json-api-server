<?php

declare(strict_types=1);

namespace JsonApi\Server\Response;

use JsonApi\ErrorDocument;
use JsonApi\ToManyDocument;
use JsonApi\ToOneDocument;
use Psr\Http\Message\ResponseInterface;
use Psr\Server\Response\AbstractResponseFactory;

final readonly class Response extends AbstractResponseFactory
{
    public static function ok(ToOneDocument|ToManyDocument $document): ResponseInterface
    {
        return (new self(
            200,
            ['content-type' => 'application/vnd.api+json'],
            (string) json_encode($document->serialize(), JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION)
        ))->__invoke();
    }

    public static function created(ToOneDocument $document): ResponseInterface
    {
        return (new self(
            201,
            ['content-type' => 'application/vnd.api+json'],
            (string) json_encode($document->serialize(), JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION)
        ))->__invoke();
    }

    public static function accepted(ToOneDocument $document): ResponseInterface
    {
        return (new self(
            202,
            ['content-type' => 'application/vnd.api+json'],
            (string) json_encode($document->serialize(), JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION)
        ))->__invoke();
    }

    public static function noContent(): ResponseInterface
    {
        return (new self(
            204
        ))->__invoke();
    }

    public static function error(ErrorDocument $document, int $code = 500): ResponseInterface
    {
        return (new self(
            $code,
            ['content-type' => 'application/vnd.api+json'],
            (string) json_encode($document->serialize(), JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION)
        ))->__invoke();
    }
}
