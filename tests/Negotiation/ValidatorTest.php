<?php

declare(strict_types=1);

namespace JsonApi\Server\Tests\Negotiation;

use JsonApi\Server\Negotiation\BodyValidator;
use JsonApi\Server\Negotiation\Exception\MalformedContentException;
use JsonApi\Server\Negotiation\Exception\MalformedQueryParamException;
use JsonApi\Server\Negotiation\Exception\MismatchMediaTypeException;
use JsonApi\Server\Negotiation\Exception\NotAcceptableMediaTypeException;
use JsonApi\Server\Negotiation\Exception\UnsupportedMediaTypeException;
use JsonApi\Server\Negotiation\Header\AcceptValidator;
use JsonApi\Server\Negotiation\Header\ContentTypeValidator;
use JsonApi\Server\Negotiation\Query\FieldsValidator;
use JsonApi\Server\Negotiation\Query\FilterValidator;
use JsonApi\Server\Negotiation\Query\IncludeValidator;
use JsonApi\Server\Negotiation\Query\PageValidator;
use JsonApi\Server\Negotiation\Query\SortValidator;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Validator\ValidatorChain;

final class ValidatorTest extends TestCase
{
    public static function malformed_request_query_params_provider(): array
    {
        return [
            [new ServerRequest('GET', 'https://www.example.com/articles?include=author&fields[author]=firstname+surname')],
            [new ServerRequest('GET', 'https://www.example.com/articles?include=author&fields=author.firstname,author.surname')],
            [new ServerRequest('GET', 'https://www.example.com/articles?include=author&filter[eq:author+status]=active')],
            [new ServerRequest('GET', 'https://www.example.com/articles?include=author&filter=eq:author.status.active')],
            [new ServerRequest('GET', 'https://www.example.com/articles?include=author+comments')],
            [new ServerRequest('GET', 'https://www.example.com/articles?include[author]=comments')],
            [new ServerRequest('GET', 'https://www.example.com/articles?page[offset]=first&page[limit]=ten')],
            [new ServerRequest('GET', 'https://www.example.com/articles?page[number]=1&page[size]=10')],
            [new ServerRequest('GET', 'https://www.example.com/articles?sort=+created_at,id')],
            [new ServerRequest('GET', 'https://www.example.com/articles?sort[created_at]=desc&sort[id]=asc')],
        ];
    }

    #[DataProvider('malformed_request_query_params_provider')]
    public function test_must_throw_malformed_query_param_exception(ServerRequestInterface $request): void
    {
        self::expectException(MalformedQueryParamException::class);

        $validator = new ValidatorChain(
            new FieldsValidator(),
            new FilterValidator(),
            new IncludeValidator(),
            new PageValidator(),
            new SortValidator(),
        );

        $validator->__invoke($request);
    }

    #[DoesNotPerformAssertions]
    public function test_must_noop_query_params_validator(): void
    {
        $validator = new ValidatorChain(
            new FieldsValidator(),
            new FilterValidator(),
            new IncludeValidator(),
            new PageValidator(),
            new SortValidator(),
        );

        $request = new Response(200);

        $validator->__invoke($request);
    }

    public function test_must_throw_not_acceptable_media_type_exception(): void
    {
        self::expectException(NotAcceptableMediaTypeException::class);

        $validator = new AcceptValidator();

        $request = new ServerRequest('GET', 'https://www.example.com/articles');

        $validator->__invoke($request);
    }

    public function test_must_throw_unsupported_media_type_exception(): void
    {
        self::expectException(UnsupportedMediaTypeException::class);

        $validator = new ContentTypeValidator();

        $request = new ServerRequest('POST', 'https://www.example.com/articles', ['Accept' => 'application/json', 'Content-Type' => 'application/json']);

        $validator->__invoke($request);
    }

    public function test_must_throw_mismatch_media_type_exception(): void
    {
        self::expectException(MismatchMediaTypeException::class);

        $validator = new BodyValidator((object) [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'type' => 'object',
            'properties' => (object) [
                'data' => (object) [
                    'type' => 'object',
                ],
            ],
            'additionalProperties' => false,
        ]);

        $request = new ServerRequest(
            'POST',
            'https://www.example.com/articles',
            ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            <<<'HTML'
            <!DOCTYPE html>
            HTML
        );

        $validator->__invoke($request);
    }

    public function test_must_throw_malformed_content_exception(): void
    {
        self::expectException(MalformedContentException::class);

        $validator = new BodyValidator((object) [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'type' => 'object',
            'properties' => (object) [
                'data' => (object) [
                    'type' => 'object',
                ],
            ],
            'additionalProperties' => false,
        ]);

        $request = new ServerRequest(
            'POST',
            'https://www.example.com/articles',
            ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            <<<'JSON'
            {
              "data": "invalid data"
            }
            JSON
        );

        $validator->__invoke($request);
    }
}
