<?php

declare(strict_types=1);

namespace JsonApi\Server\Request;

use JsonApi\Validator\BodyValidator;
use JsonApi\Validator\Header\AcceptValidator;
use JsonApi\Validator\Header\ContentTypeValidator;
use JsonApi\Validator\QueryParam\FieldsValidator;
use JsonApi\Validator\QueryParam\FilterValidator;
use JsonApi\Validator\QueryParam\IncludeValidator;
use JsonApi\Validator\QueryParam\PaginationValidator;
use JsonApi\Validator\QueryParam\SortingValidator;
use JsonApi\Validator\QueryParam\SupportedQueryParamsValidator;
use Psr\Server\Middleware\MiddlewareRunner;
use Psr\Server\Request\AbstractRequestHandler;
use Psr\Validator\Middleware\ValidationMiddleware;
use Psr\Validator\ResponseValidator;
use Psr\Validator\Schema\Schema;
use Psr\Validator\Schema\SchemaFactory\JsonFileFactory;
use Psr\Validator\ServerRequestValidator;

final readonly class Request extends AbstractRequestHandler
{
    private function __construct(
        ServerRequestValidator $serverRequestValidator,
        ResponseValidator $responseValidator,
        \Closure $handler,
    ) {
        parent::__construct(
            new MiddlewareRunner(new ValidationMiddleware(
                $serverRequestValidator,
                $responseValidator
            )),
            $handler
        );
    }

    public static function relationship(\Closure $handler): self
    {
        return new self(
            new ServerRequestValidator(
                new ContentTypeValidator(),
                new AcceptValidator(),
                new SupportedQueryParamsValidator([]),
                new BodyValidator(
                    new Schema(
                        (new JsonFileFactory(__DIR__.'/../../schema/json-api/v1.1/relationship.json'))->__invoke()
                    )
                )
            ),
            new ResponseValidator(
                new ContentTypeValidator(),
                new BodyValidator(
                    new Schema(
                        (new JsonFileFactory(__DIR__.'/../../schema/json-api/v1.1/schema.json'))->__invoke()
                    )
                )
            ),
            $handler
        );
    }

    public static function fetchOne(\Closure $handler): self
    {
        return new self(
            new ServerRequestValidator(
                new AcceptValidator(),
                new SupportedQueryParamsValidator([FieldsValidator::NAME, IncludeValidator::NAME]),
                new FieldsValidator(),
                new IncludeValidator()
            ),
            new ResponseValidator(
                new ContentTypeValidator(),
                new BodyValidator(
                    new Schema(
                        (new JsonFileFactory(__DIR__.'/../../schema/json-api/v1.1/schema.json'))->__invoke()
                    )
                )
            ),
            $handler
        );
    }

    public static function fetchMany(\Closure $handler): self
    {
        return new self(
            new ServerRequestValidator(
                new AcceptValidator(),
                new SupportedQueryParamsValidator(),
                new FieldsValidator(),
                new IncludeValidator(),
                new SortingValidator(),
                new PaginationValidator(),
                new FilterValidator()
            ),
            new ResponseValidator(
                new ContentTypeValidator(),
                new BodyValidator(
                    new Schema(
                        (new JsonFileFactory(__DIR__.'/../../schema/json-api/v1.1/schema.json'))->__invoke()
                    )
                )
            ),
            $handler
        );
    }

    public static function resource(\Closure $handler): self
    {
        return new self(
            new ServerRequestValidator(
                new ContentTypeValidator(),
                new AcceptValidator(),
                new SupportedQueryParamsValidator([]),
                new BodyValidator(
                    new Schema(
                        (new JsonFileFactory(__DIR__.'/../../schema/json-api/v1.1/resource.json'))->__invoke()
                    )
                )
            ),
            new ResponseValidator(
                new ContentTypeValidator(),
                new BodyValidator(
                    new Schema(
                        (new JsonFileFactory(__DIR__.'/../../schema/json-api/v1.1/schema.json'))->__invoke()
                    )
                )
            ),
            $handler
        );
    }
}
