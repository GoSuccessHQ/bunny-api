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

    /** Returns the headers of a response without its body, e.g. the offset of a TUS upload. */
    case Head = 'HEAD';

    /**
     * Returns the metadata of an Edge Storage object. Not part of any
     * specification; bunny.net's own CLI uses it.
     */
    case Describe = 'DESCRIBE';

    /**
     * Whether repeating the request has the same effect as sending it once.
     *
     * bunny.net uses POST for most updates, which are therefore never retried
     * automatically after a server error or a lost connection.
     */
    public function isIdempotent(): bool
    {
        return match ($this) {
            self::Get, self::Put, self::Delete, self::Head, self::Describe => true,
            self::Post, self::Patch => false,
        };
    }
}
