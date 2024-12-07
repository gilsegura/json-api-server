<?php

declare(strict_types=1);

namespace JsonApi\Server\Negotiation;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Server\ResponseFactory\Status;

trait MessageNoContentTrait
{
    protected function hasContent(MessageInterface $message): bool
    {
        return
            (
                $message instanceof RequestInterface
                && !in_array($message->getMethod(), ['CONNECT', 'DELETE', 'GET', 'HEAD', 'OPTIONS', 'TRACE'], true)
            )
            || (
                $message instanceof ResponseInterface
                && !in_array($message->getStatusCode(), [Status::CONTINUE, Status::SWITCHING_PROTOCOLS, Status::PROCESSING, Status::NO_CONTENT, Status::RESET_CONTENT, Status::NOT_MODIFIED], true)
            );
    }
}
