<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation\Header;

use JsonApi\Server\Negotiation\Exception\NotAcceptableMediaTypeException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Validator\MessageValidatorInterface;

final readonly class AcceptValidator implements MessageValidatorInterface
{
    #[\Override]
    public function __invoke(MessageInterface $message): MessageInterface
    {
        if (!$message instanceof RequestInterface) {
            return $message;
        }

        if (!preg_match('#^.*application/vnd\.api\+json(?:\s*;\s*(?:ext|profile)\s*=\s*".*")?$#', $message->getHeaderLine('accept'))) {
            throw NotAcceptableMediaTypeException::notAcceptable();
        }

        return $message;
    }
}
