<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Header;

use JsonApi\Server\Negotiation\Exception\UnsupportedMediaTypeException;
use JsonApi\Server\Negotiation\MessageNoContentTrait;
use Psr\Http\Message\MessageInterface;
use Psr\Validator\MessageValidatorInterface;

final readonly class ContentTypeValidator implements MessageValidatorInterface
{
    use MessageNoContentTrait;

    #[\Override]
    public function __invoke(MessageInterface $message): MessageInterface
    {
        if (!$this->hasContent($message)) {
            return $message;
        }

        if (!preg_match('#^.*application/vnd\.api\+json(?:\s*;\s*(?:ext|profile)\s*=\s*".*")?$#', $message->getHeaderLine('content-type'))) {
            throw UnsupportedMediaTypeException::unsupported();
        }

        return $message;
    }
}
