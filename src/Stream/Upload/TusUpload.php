<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Stream\Upload;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use SensitiveParameter;

/**
 * Presigned credentials for a resumable TUS upload to Bunny Stream.
 *
 * Create the video on your server, presign the upload there, and hand the
 * endpoint and headers to a TUS client (e.g. tus-js-client in the browser),
 * which then uploads directly to bunny.net without ever seeing the API key.
 * To upload from PHP, use {@see TusUploader}.
 *
 * ```php
 * $video = $stream->videos->create('My video');
 * $upload = TusUpload::presign($libraryId, $libraryApiKey, $video->guid, new DateTimeImmutable('+1 day'));
 *
 * return ['endpoint' => $upload->endpoint, 'headers' => $upload->headers];
 * ```
 */
final readonly class TusUpload
{
    public const string ENDPOINT = 'https://video.bunnycdn.com/tusupload';

    /**
     * @param string                $endpoint The TUS endpoint to upload to.
     * @param array<string, string> $headers  The headers the TUS client must send:
     *                                        AuthorizationSignature, AuthorizationExpire,
     *                                        LibraryId and VideoId.
     * @param DateTimeImmutable     $expires  When the credentials expire; the upload must
     *                                        be finished by then.
     */
    public function __construct(
        public string $endpoint,
        public array $headers,
        public DateTimeImmutable $expires,
    ) {}

    /**
     * Sign an upload of the video with the given GUID.
     *
     * The signature is SHA-256 over library ID, API key, expiry and video GUID,
     * as bunny.net documents it. bunny.net recommends an expiry of at least an
     * hour, so that large uploads finish in time.
     *
     * @param int               $libraryId The ID of the video library.
     * @param string            $apiKey    The API key of the video library.
     * @param string            $videoId   The GUID of a video created beforehand.
     * @param DateTimeInterface $expires   When the credentials expire.
     */
    public static function presign(
        int $libraryId,
        #[SensitiveParameter]
        string $apiKey,
        string $videoId,
        DateTimeInterface $expires,
    ): self {
        if (trim($videoId) === '') {
            throw new InvalidArgumentException('The video GUID must not be empty.');
        }

        $expiresAt = $expires->getTimestamp();

        return new self(
            endpoint: self::ENDPOINT,
            headers: [
                'AuthorizationSignature' => hash('sha256', "{$libraryId}{$apiKey}{$expiresAt}{$videoId}"),
                'AuthorizationExpire' => (string) $expiresAt,
                'LibraryId' => (string) $libraryId,
                'VideoId' => $videoId,
            ],
            expires: DateTimeImmutable::createFromInterface($expires),
        );
    }

    /**
     * The TUS `Upload-Metadata` header for the upload: base64-encoded pairs of
     * the video's file type, title and optional collection and thumbnail time.
     *
     * @param string      $fileType      The media type of the file, e.g. `video/mp4`.
     * @param string      $title         The title of the video.
     * @param string|null $collectionId  The GUID of the collection to put the video in.
     * @param int|null    $thumbnailTime The video time in milliseconds to take the thumbnail from.
     */
    public static function metadata(string $fileType, string $title, ?string $collectionId = null, ?int $thumbnailTime = null): string
    {
        $pairs = ['filetype' => $fileType, 'title' => $title, 'collection' => $collectionId, 'thumbnailTime' => $thumbnailTime === null ? null : (string) $thumbnailTime];
        $encoded = [];

        foreach ($pairs as $key => $value) {
            if ($value !== null) {
                $encoded[] = "{$key} " . base64_encode($value);
            }
        }

        return implode(',', $encoded);
    }
}
