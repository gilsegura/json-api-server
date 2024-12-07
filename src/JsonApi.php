<?php

declare(strict_types=1);

namespace JsonApi\Server;

use JsonApi\Server\Negotiation\BodyValidator;
use JsonApi\Server\Negotiation\Header\AcceptValidator;
use JsonApi\Server\Negotiation\Header\ContentTypeValidator;
use JsonApi\Server\Negotiation\Query\FieldsValidator;
use JsonApi\Server\Negotiation\Query\FilterValidator;
use JsonApi\Server\Negotiation\Query\IncludeValidator;
use JsonApi\Server\Negotiation\Query\PageValidator;
use JsonApi\Server\Negotiation\Query\SortValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Server\Middleware\MiddlewareRunner;
use Psr\Server\RequestHandler;
use Psr\Validator\Middleware\ValidationMiddleware;
use Psr\Validator\ResponseValidator;
use Psr\Validator\SchemaFactory\FileFactory;
use Psr\Validator\ServerRequestValidator;
use Psr\Validator\ValidatorChain;

final readonly class JsonApi
{
    /** @var MiddlewareInterface[] */
    private array $middlewares;

    public function __construct(MiddlewareInterface ...$middlewares)
    {
        $this->middlewares = [
            ...$middlewares,
            ...[
                new ValidationMiddleware(
                    new ServerRequestValidator(
                        new ValidatorChain(
                            new ContentTypeValidator(),
                            new AcceptValidator(),
                        ),
                        new ValidatorChain(
                            new FieldsValidator(),
                            new IncludeValidator(),
                            new SortValidator(),
                            new PageValidator(),
                            new FilterValidator(),
                        ),
                        new BodyValidator(
                            (new FileFactory(__DIR__.'/../schemas/datum.json'))->__invoke()
                        ),
                    ),
                    new ResponseValidator(
                        new ContentTypeValidator(),
                        new BodyValidator(
                            (new FileFactory(__DIR__.'/../schemas/schema.json'))->__invoke()
                        ),
                    ),
                ),
            ],
        ];
    }

    public function __invoke(ServerRequestInterface $request, callable $handler): ResponseInterface
    {
        return RequestHandler::callable(new MiddlewareRunner(...$this->middlewares), $handler)
            ->__invoke($request);
    }
}
