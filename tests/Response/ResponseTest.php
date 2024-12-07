<?php

declare(strict_types=1);

namespace JsonApi\Server\Tests\Response;

use JsonApi\Server\Definition\ErrorDocument;
use JsonApi\Server\Response\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ResponseTest extends TestCase
{
    public function test_must_return_error_response(): void
    {
        $response = Response::error(
            ErrorDocument::document()
        );

        self::assertInstanceOf(ResponseInterface::class, $response);
    }
}
