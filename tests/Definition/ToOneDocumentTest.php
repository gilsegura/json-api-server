<?php

declare(strict_types=1);

namespace JsonApi\Server\Tests\Definition;

use JsonApi\Server\Definition\Attribute\Attribute;
use JsonApi\Server\Definition\Data;
use JsonApi\Server\Definition\Link\Link;
use JsonApi\Server\Definition\Meta\Meta;
use JsonApi\Server\Definition\Relationship\ToManyRelationship;
use JsonApi\Server\Definition\Relationship\ToOneRelationship;
use JsonApi\Server\Definition\Resource\Resource;
use JsonApi\Server\Definition\Resource\ResourceIdentifier;
use JsonApi\Server\Definition\ToOneDocument;
use PHPUnit\Framework\TestCase;

final class ToOneDocumentTest extends TestCase
{
    public function test_must_serialize_to_one_document_resource(): void
    {
        $document = ToOneDocument::document()
            ->withData(
                Data::fromResource(
                    Resource::resource('5af3a4a8-a528-4dec-8285-174ef458b6bd', 'articles')
                        ->withAttributes(
                            Attribute::kv('title', 'JSON:API paints my bikeshed!')
                        )
                        ->withRelationships(
                            ToOneRelationship::relationship(
                                'author',
                                ResourceIdentifier::resource('e0876f63-0656-4ff2-91c3-dbd680864745', 'people')
                            )
                                ->withLinks(
                                    Link::self('https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd/relationships/author'),
                                    Link::related('https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd/author')
                                ),
                            ToManyRelationship::relationship(
                                'comments',
                                [
                                    ResourceIdentifier::resource('32de4efb-e08d-4521-8e4e-0f008ed139f0', 'comments'),
                                    ResourceIdentifier::resource('f37913d3-b0a8-45d9-a007-8f83408c8736', 'comments'),
                                ]
                            )
                                ->withLinks(
                                    Link::self('https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd/relationships/comments'),
                                    Link::related('https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd/comments')
                                )
                        )
                        ->withLink(
                            Link::self('https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd')
                        )
                )
            )
            ->withMeta(
                Meta::kv('copyright', 'Copyright (c) 2024 https://github.com/gilsegura')
            )
            ->withJsonapi(
                Meta::kv('version', '1.1')
            )
            ->withLinks(
                Link::self('https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd'),
                Link::describedby('https://www.example.com/doc')
            )
            ->withIncluded(
                Resource::resource('e0876f63-0656-4ff2-91c3-dbd680864745', 'people')
                    ->withAttributes(Attribute::kv('name', 'John Doe')),
                Resource::resource('32de4efb-e08d-4521-8e4e-0f008ed139f0', 'comments'),
                Resource::resource('f37913d3-b0a8-45d9-a007-8f83408c8736', 'comments')
            );

        $serialized = [
            'meta' => [
                'copyright' => 'Copyright (c) 2024 https://github.com/gilsegura',
            ],
            'jsonapi' => [
                'version' => '1.1',
            ],
            'links' => [
                'self' => 'https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd',
                'describedby' => 'https://www.example.com/doc',
            ],
            'data' => [
                'type' => 'articles',
                'id' => '5af3a4a8-a528-4dec-8285-174ef458b6bd',
                'attributes' => [
                    'title' => 'JSON:API paints my bikeshed!',
                ],
                'relationships' => [
                    'author' => [
                        'links' => [
                            'self' => 'https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd/relationships/author',
                            'related' => 'https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd/author',
                        ],
                        'data' => [
                            'type' => 'people',
                            'id' => 'e0876f63-0656-4ff2-91c3-dbd680864745',
                        ],
                    ],
                    'comments' => [
                        'links' => [
                            'self' => 'https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd/relationships/comments',
                            'related' => 'https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd/comments',
                        ],
                        'data' => [
                            [
                                'type' => 'comments',
                                'id' => '32de4efb-e08d-4521-8e4e-0f008ed139f0',
                            ],
                            [
                                'type' => 'comments',
                                'id' => 'f37913d3-b0a8-45d9-a007-8f83408c8736',
                            ],
                        ],
                    ],
                ],
                'links' => [
                    'self' => 'https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd',
                ],
            ],
            'included' => [
                [
                    'type' => 'people',
                    'id' => 'e0876f63-0656-4ff2-91c3-dbd680864745',
                    'attributes' => [
                        'name' => 'John Doe',
                    ],
                ],
                [
                    'type' => 'comments',
                    'id' => '32de4efb-e08d-4521-8e4e-0f008ed139f0',
                ],
                [
                    'type' => 'comments',
                    'id' => 'f37913d3-b0a8-45d9-a007-8f83408c8736',
                ],
            ],
        ];

        self::assertInstanceOf(ToOneDocument::class, $document);
        self::assertSame($serialized, $document->serialize());
        self::assertEquals($document, ToOneDocument::deserialize($serialized));
    }

    public function test_must_serialize_to_one_document_resource_identifier(): void
    {
        $document = ToOneDocument::document()
            ->withData(
                Data::fromResource(
                    ResourceIdentifier::resource('5af3a4a8-a528-4dec-8285-174ef458b6bd', 'articles')
                )
            )
            ->withMeta(
                Meta::kv('copyright', 'Copyright (c) 2024 https://github.com/gilsegura')
            )
            ->withJsonapi(
                Meta::kv('version', '1.1')
            )
            ->withLinks(
                Link::self('https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd'),
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
                'self' => 'https://example.com/articles/5af3a4a8-a528-4dec-8285-174ef458b6bd',
                'describedby' => 'https://www.example.com/doc',
            ],
            'data' => [
                'type' => 'articles',
                'id' => '5af3a4a8-a528-4dec-8285-174ef458b6bd',
            ],
        ];

        self::assertInstanceOf(ToOneDocument::class, $document);
        self::assertSame($serialized, $document->serialize());
        self::assertEquals($document, ToOneDocument::deserialize($serialized));
    }
}
