<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Http;

/**
 * HTTP methods used by the bunny.net APIs.
 */
enum Method: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Patch = 'PATCH';
    case Delete = 'DELETE';

    /**
     * Whether repeating the request has the same effect as sending it once.
     *
     * bunny.net uses POST for most updates, which are therefore never retried
     * automatically after a server error or a lost connection.
     */
    public function isIdempotent(): bool
    {
        return match ($this) {
            self::Get, self::Put, self::Delete => true,
            self::Post, self::Patch => false,
        };
    }
}
