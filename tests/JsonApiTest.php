<?php

declare(strict_types=1);

namespace JsonApi\Server\Tests;

use JsonApi\Server\Definition\ToManyDocument;
use JsonApi\Server\Definition\ToOneDocument;
use JsonApi\Server\JsonApi;
use JsonApi\Server\Response\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Server\ResponseFactory\Status;

final class JsonApiTest extends TestCase
{
    public function test_must_handle_delete(): void
    {
        $server = new JsonApi();

        $request = new ServerRequest(
            'DELETE',
            'https://www.example.com/photos/7f119440-a4a1-4226-9b8e-f0f7cbe7bc21',
            ['Accept' => 'application/vnd.api+json']
        );

        $response = $server->__invoke(
            $request,
            static fn (ServerRequestInterface $request): ResponseInterface => Response::noContent()
        );

        self::assertSame(Status::NO_CONTENT, $response->getStatusCode());
        self::assertFalse($response->hasHeader('Content-Type'));
    }

    public function test_must_handle_get(): void
    {
        $server = new JsonApi();

        $request = new ServerRequest(
            'GET',
            'https://www.example.com/photos?filter[eq:status]=active&sort=-created_at,id&page[offset]=1&page[limit]=10',
            ['Accept' => 'application/vnd.api+json']
        );

        $response = $server->__invoke(
            $request,
            static function (ServerRequestInterface $request): ResponseInterface {
                return Response::ok(ToManyDocument::document());
            }
        );

        self::assertSame(Status::OK, $response->getStatusCode());
        self::assertSame('application/vnd.api+json', $response->getHeaderLine('Content-Type'));
    }

    public function test_must_handle_patch(): void
    {
        $server = new JsonApi();

        $request = new ServerRequest(
            'POST',
            'https://www.example.com/photos',
            ['Accept' => 'application/vnd.api+json', 'Content-Type' => 'application/vnd.api+json'],
            <<<'JSON'
            {
                "data": {
                    "type": "articles",
                    "id": "7f119440-a4a1-4226-9b8e-f0f7cbe7bc21",
                    "attributes": {
                        "title": "To TDD or Not",
                        "text": "TLDR; It's complicated... but check your test coverage regardless."
                    }
                }
            }
            JSON
        );

        $response = $server->__invoke(
            $request,
            static fn (ServerRequestInterface $request): ResponseInterface => Response::accepted(ToOneDocument::document())
        );

        self::assertSame(Status::ACCEPTED, $response->getStatusCode());
        self::assertSame('application/vnd.api+json', $response->getHeaderLine('Content-Type'));
    }

    public function test_must_handle_post(): void
    {
        $server = new JsonApi();

        $request = new ServerRequest(
            'PATCH',
            'https://www.example.com/photos/7f119440-a4a1-4226-9b8e-f0f7cbe7bc21',
            ['Accept' => 'application/vnd.api+json', 'Content-Type' => 'application/vnd.api+json'],
            <<<'JSON'
            {
                "data": {
                    "type": "photos",
                    "id": "7f119440-a4a1-4226-9b8e-f0f7cbe7bc21",
                    "attributes": {
                        "title": "Ember Hamster",
                        "src": "https://example.com/images/productivity.png"
                    }
                }
            }
            JSON
        );

        $expected = Response::created(
            ToOneDocument::document()
        );

        $response = $server->__invoke(
            $request,
            static fn (ServerRequestInterface $request): ResponseInterface => $expected
        );

        self::assertSame(Status::CREATED, $response->getStatusCode());
        self::assertSame('application/vnd.api+json', $response->getHeaderLine('Content-Type'));
    }
}
