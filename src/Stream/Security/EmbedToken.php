<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Stream\Security;

use DateTimeInterface;
use GoSuccess\Bunny\Http\Query;
use SensitiveParameter;

/**
 * Signs embed URLs for libraries with embed view token authentication.
 *
 * When token authentication is enabled, every embed (and every play data
 * request) needs a `token` and `expires` pair. The token is the SHA-256 hex
 * digest of the library's token authentication key, the video GUID and the
 * expiry, as bunny.net documents it.
 *
 * ```php
 * $url = EmbedToken::url($libraryId, $videoGuid, $tokenKey, new DateTimeImmutable('+1 hour'));
 * ```
 */
final class EmbedToken
{
    public const string EMBED_BASE_URI = 'https://iframe.mediadelivery.net/embed';

    /**
     * The token for a video and expiry.
     *
     * @param string            $tokenKey The library's token authentication key
     *                                    (dashboard → Stream → library → Security).
     * @param string            $videoId  The GUID of the video.
     * @param DateTimeInterface $expires  Until when the token is valid.
     */
    public static function sign(
        #[SensitiveParameter]
        string $tokenKey,
        string $videoId,
        DateTimeInterface $expires,
    ): string {
        return hash('sha256', "{$tokenKey}{$videoId}{$expires->getTimestamp()}");
    }

    /**
     * A signed embed URL for the iframe player.
     *
     * @param int                             $libraryId  The ID of the video library.
     * @param string                          $videoId    The GUID of the video.
     * @param string                          $tokenKey   The library's token authentication key.
     * @param DateTimeInterface               $expires    Until when the URL is valid.
     * @param array<string, string|int|bool>  $parameters Further player parameters, e.g. `['autoplay' => true]`.
     */
    public static function url(
        int $libraryId,
        string $videoId,
        #[SensitiveParameter]
        string $tokenKey,
        DateTimeInterface $expires,
        array $parameters = [],
    ): string {
        $query = Query::build([
            ...$parameters,
            'token' => self::sign($tokenKey, $videoId, $expires),
            'expires' => $expires->getTimestamp(),
        ]);

        return self::EMBED_BASE_URI . "/{$libraryId}/" . rawurlencode($videoId) . "?{$query}";
    }
}
