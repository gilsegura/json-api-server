<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation;

use JsonApi\Server\Negotiation\Exception\MalformedContentException;
use JsonApi\Server\Negotiation\Exception\MismatchMediaTypeException;
use Psr\Http\Message\MessageInterface;
use Psr\Validator\MessageValidatorInterface;
use Psr\Validator\Schema\SchemaValidator;

final readonly class BodyValidator implements MessageValidatorInterface
{
    use MessageNoContentTrait;

    public function __construct(
        private object $schema,
    ) {
    }

    #[\Override]
    public function __invoke(MessageInterface $message): MessageInterface
    {
        if (!$this->hasContent($message)) {
            return $message;
        }

        $stream = $message->getBody();
        $body = $stream->__toString();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        if (!json_validate($body)) {
            throw MismatchMediaTypeException::mismatch();
        }

        try {
            $json = (object) json_decode($body, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw MismatchMediaTypeException::mismatch();
        }

        $errors = (new SchemaValidator())->__invoke(clone $json, clone $this->schema);

        if ([] !== $errors) {
            throw MalformedContentException::malformed($errors);
        }

        return $message;
    }
}
