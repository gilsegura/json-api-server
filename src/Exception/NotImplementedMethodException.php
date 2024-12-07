<?php

declare(strict_types=1);

namespace JsonApi\Server\Exception;

final class NotImplementedMethodException extends \Exception
{
    public static function method(string $method): self
    {
        return new self(sprintf('The requested method "%s" does not implemented', $method), 501);
    }
}
