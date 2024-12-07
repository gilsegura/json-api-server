<?php

declare(strict_types=1);

namespace JsonApi\Server\Tests\Definition;

use JsonApi\Server\Definition\Error\Error;
use JsonApi\Server\Definition\ErrorDocument;
use JsonApi\Server\Definition\Link\Link;
use JsonApi\Server\Definition\Meta\Meta;
use JsonApi\Server\Definition\Source\Source;
use PHPUnit\Framework\TestCase;

final class ErrorDocumentTest extends TestCase
{
    public function test_must_serialize_error_document(): void
    {
        $document = ErrorDocument::document(
            Error::error(
                '406',
                'not_acceptable_media_type',
                'No acceptable media type',
                'The provided media type does not accepted.'
            )
                ->withSource(Source::header('accept'))
                ->withLink(Link::about('https://www.example.com/doc/errors/406')),
            Error::error(
                '400',
                'malformed_query_param',
                'Malformed query param',
                'The provided query-param "sort" does not valid.'
            )
                ->withSource(Source::parameter('sort')),
            Error::error(
                '400',
                'malformed_content',
                'Malformed content',
                'The provided content is malformed.'
            )
                ->withSource(Source::pointer('/data/attributes/src'))
        )
            ->withMeta(
                Meta::kv('copyright', 'Copyright (c) 2024 https://github.com/gilsegura')
            )
            ->withJsonapi(
                Meta::kv('version', '1.1')
            )
            ->withLinks(
                Link::self('https://www.example.com/photos'),
                Link::describedby('https://www.example.com/doc')
            );

        $serialized = [
            'meta' => [
                'copyright' => 'Copyright (c) 2024 https://github.com/gilsegura',
            ],
            'jsonapi' => [
                'version' => '1.1',
            ],
            'links' => [
                'self' => 'https://www.example.com/photos',
                'describedby' => 'https://www.example.com/doc',
            ],
            'errors' => [
                [
                    'status' => '406',
                    'code' => 'not_acceptable_media_type',
                    'title' => 'No acceptable media type',
                    'detail' => 'The provided media type does not accepted.',
                    'source' => [
                        'header' => 'accept',
                    ],
                    'links' => [
                        'about' => 'https://www.example.com/doc/errors/406',
                    ],
                ],
                [
                    'status' => '400',
                    'code' => 'malformed_query_param',
                    'title' => 'Malformed query param',
                    'detail' => 'The provided query-param "sort" does not valid.',
                    'source' => [
                        'parameter' => 'sort',
                    ],
                ],
                [
                    'status' => '400',
                    'code' => 'malformed_content',
                    'title' => 'Malformed content',
                    'detail' => 'The provided content is malformed.',
                    'source' => [
                        'pointer' => '/data/attributes/src',
                    ],
                ],
            ],
        ];

        self::assertInstanceOf(ErrorDocument::class, $document);
        self::assertSame($serialized, $document->serialize());
        self::assertEquals($document, ErrorDocument::deserialize($serialized));
    }
}
