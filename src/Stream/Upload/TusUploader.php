<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Stream\Upload;

use Closure;
use DateTimeImmutable;
use DateTimeInterface;
use GoSuccess\Bunny\ClientOptions;
use GoSuccess\Bunny\Exception\ApiException;
use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Exception\TransportException;
use GoSuccess\Bunny\Http\CurlHttpClient;
use GoSuccess\Bunny\Http\HttpClient;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Request;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\RateLimit\Clock;
use GoSuccess\Bunny\RateLimit\NullRateLimiter;
use GoSuccess\Bunny\RateLimit\RateLimiter;
use GoSuccess\Bunny\RateLimit\SystemClock;
use InvalidArgumentException;
use SensitiveParameter;

/**
 * Resumable uploads to Bunny Stream from PHP, over the TUS protocol.
 *
 * The file travels in chunks, each with a SHA-1 checksum the server verifies.
 * After a network failure or a server error the uploader asks the server how
 * much arrived and continues from there, waiting 0, 3, 5, 10, 20, 60 and 60
 * seconds between attempts, like the TUS client bunny.net recommends. An upload
 * can also be resumed later, even from another process, with its TusSession.
 *
 * bunny.net recommends TUS for files over 2 GB and for unstable connections;
 * videos->upload() of the Stream API cannot resume.
 *
 * ```php
 * $video = $bunny->stream($libraryId, $apiKey)->videos->create('Product tour');
 *
 * $bunny->streamUploader($libraryId, $apiKey)->upload(
 *     $video->guid,
 *     Stream::fromFile('tour.mp4'),
 *     fileType: 'video/mp4',
 *     title: 'Product tour',
 * );
 * ```
 */
final class TusUploader
{
    /**
     * Bytes per request. Small enough for the common request size limits of
     * servers and proxies, large enough to keep the round trips few.
     */
    public const int DEFAULT_CHUNK_SIZE = 16 * 1024 * 1024;

    /**
     * Seconds to wait before each attempt after a failure: the defaults of
     * tus-js-client, which bunny.net's documentation uses.
     */
    public const array DEFAULT_RETRY_DELAYS = [0.0, 3.0, 5.0, 10.0, 20.0, 60.0, 60.0];

    private const string PROTOCOL = '1.0.0';

    /** Bytes hashed at a time for the checksum of a chunk. */
    private const int HASH_BUFFER = 1024 * 1024;

    private readonly HttpClient $httpClient;

    /**
     * @param int           $libraryId   The ID of the video library.
     * @param string        $apiKey      The API key of the video library. It only signs the
     *                                   upload and is never sent.
     * @param ClientOptions $options     Timeouts and user agent; chunks count as transfers.
     * @param HttpClient|null $httpClient Custom transport; defaults to the built-in cURL transport.
     * @param RateLimiter   $rateLimiter Client-side throttling; disabled by default.
     * @param int           $chunkSize   Bytes per request. A failed chunk is sent again,
     *                                   so smaller chunks lose less on a bad connection.
     * @param list<float>   $retryDelays Seconds to wait before each attempt after a failure;
     *                                   the count starts over whenever the upload progresses.
     * @param bool          $checksums   Send a SHA-1 checksum with every chunk. bunny.net's
     *                                   endpoint supports it; turn it off for a server that
     *                                   does not.
     * @param non-empty-string $endpoint The TUS endpoint uploads are created at.
     * @param Clock         $clock       Source of time for the waits between attempts.
     */
    public function __construct(
        private readonly int $libraryId,
        #[SensitiveParameter]
        private readonly string $apiKey,
        private readonly ClientOptions $options = new ClientOptions(),
        ?HttpClient $httpClient = null,
        private readonly RateLimiter $rateLimiter = new NullRateLimiter(),
        private readonly int $chunkSize = self::DEFAULT_CHUNK_SIZE,
        private readonly array $retryDelays = self::DEFAULT_RETRY_DELAYS,
        private readonly bool $checksums = true,
        private readonly string $endpoint = TusUpload::ENDPOINT,
        private readonly Clock $clock = new SystemClock(),
    ) {
        if ($chunkSize < 1) {
            throw new InvalidArgumentException('The chunk size must be at least one byte.');
        }

        if (trim($apiKey) === '') {
            throw new InvalidArgumentException('The API key must not be empty.');
        }

        $this->httpClient = $httpClient ?? new CurlHttpClient($options->timeout, $options->connectTimeout);
    }

    /**
     * Upload the file of a video created beforehand, e.g. with videos->create().
     *
     * @param string                              $videoId       The GUID of the video.
     * @param string|Stream                       $file          The video file, e.g. `Stream::fromFile('video.mp4')`;
     *                                                           a stream is read from its current position.
     * @param string                              $fileType      The media type of the file, e.g. `video/mp4`.
     * @param string                              $title         The title of the video.
     * @param string|null                         $collectionId  The GUID of the collection to put the video in.
     * @param int|null                            $thumbnailTime The video time in milliseconds to take the thumbnail from.
     * @param DateTimeInterface|null              $expires       Until when the upload may take; one day by default.
     *                                                           bunny.net recommends at least an hour.
     * @param (Closure(int, int): void)|null      $onProgress    Called after every chunk with the bytes uploaded
     *                                                           and the total.
     *
     * @return TusSession The finished upload.
     *
     * @throws ApiException       If the server rejects the upload, or keeps failing.
     * @throws TransportException If the connection keeps failing.
     */
    public function upload(
        string $videoId,
        string|Stream $file,
        string $fileType,
        string $title,
        ?string $collectionId = null,
        ?int $thumbnailTime = null,
        ?DateTimeInterface $expires = null,
        ?Closure $onProgress = null,
    ): TusSession {
        $stream = self::seekable($file);
        $size = $stream->size ?? throw new InvalidArgumentException('The size of the file must be known; open it with Stream::fromFile().');
        $session = $this->create($videoId, $size, $fileType, $title, $collectionId, $thumbnailTime, $expires);

        return $this->transfer($session, $stream, $stream->position() ?? 0, $onProgress);
    }

    /**
     * Announce an upload without sending data yet.
     *
     * Store the session before uploading, e.g. with toArray(), to resume the
     * upload from another process should this one end.
     *
     * @param string                 $videoId       The GUID of the video.
     * @param int                    $size          The size of the file in bytes.
     * @param string                 $fileType      The media type of the file, e.g. `video/mp4`.
     * @param string                 $title         The title of the video.
     * @param string|null            $collectionId  The GUID of the collection to put the video in.
     * @param int|null               $thumbnailTime The video time in milliseconds to take the thumbnail from.
     * @param DateTimeInterface|null $expires       Until when the upload may take; one day by default.
     */
    public function create(
        string $videoId,
        int $size,
        string $fileType,
        string $title,
        ?string $collectionId = null,
        ?int $thumbnailTime = null,
        ?DateTimeInterface $expires = null,
    ): TusSession {
        if ($size < 0) {
            throw new InvalidArgumentException('The size must not be negative.');
        }

        $expires = $expires === null
            ? new DateTimeImmutable('@' . ((int) $this->clock->now() + 86400))
            : DateTimeImmutable::createFromInterface($expires);
        $headers = [
            'Upload-Length' => (string) $size,
            'Upload-Metadata' => TusUpload::metadata($fileType, $title, $collectionId, $thumbnailTime),
        ];

        $response = $this->retrying(fn(): Response => $this->send(Method::Post, $this->endpoint, $videoId, $expires, $headers, expected: 201));
        $location = $response->header('location');

        if ($location === '') {
            throw new SerializationException('The TUS server created the upload without telling its location.');
        }

        return new TusSession($this->resolve($location), $this->libraryId, $videoId, $size, 0, $expires);
    }

    /**
     * Continue an upload from where the server stands.
     *
     * @param TusSession                     $session    The upload, e.g. restored with TusSession::fromArray().
     * @param string|Stream                  $file       The same file, positioned at its beginning.
     * @param (Closure(int, int): void)|null $onProgress Called after every chunk with the bytes uploaded
     *                                                   and the total.
     *
     * @return TusSession The finished upload.
     *
     * @throws ApiException       If the server rejects the upload, or keeps failing. An expired
     *                            upload is gone (404); it has to be started over.
     * @throws TransportException If the connection keeps failing.
     */
    public function resume(TusSession $session, string|Stream $file, ?Closure $onProgress = null): TusSession
    {
        $this->assertOwn($session);
        $stream = self::seekable($file);

        if ($stream->size !== $session->size) {
            throw new InvalidArgumentException("The file has a different size than the upload: {$stream->size} instead of {$session->size} bytes.");
        }

        return $this->transfer($this->status($session), $stream, $stream->position() ?? 0, $onProgress);
    }

    /**
     * Ask the server how far an upload got.
     *
     * @return TusSession The session with the offset the server reports.
     */
    public function status(TusSession $session): TusSession
    {
        $this->assertOwn($session);

        return $session->withOffset($this->retrying(fn(): int => $this->offset($session)));
    }

    /**
     * Delete an unfinished upload from the server. The video itself remains;
     * delete it with videos->delete() if it is no longer wanted.
     */
    public function abort(TusSession $session): void
    {
        $this->assertOwn($session);
        $this->retrying(fn(): Response => $this->send(Method::Delete, $session->url, $session->videoId, $session->expires, expected: 204));
    }

    /**
     * Hide the API key from var_dump() and print_r().
     *
     * @return array<string, int|string>
     */
    public function __debugInfo(): array
    {
        return ['libraryId' => $this->libraryId, 'endpoint' => $this->endpoint, 'apiKey' => '********'];
    }

    /**
     * Send the rest of the file chunk by chunk, catching up with the server
     * after every failure.
     *
     * @param int<0, max>                    $start Position of the first byte of the file in the stream.
     * @param (Closure(int, int): void)|null $onProgress
     */
    private function transfer(TusSession $session, Stream $stream, int $start, ?Closure $onProgress): TusSession
    {
        $offset = $session->offset;
        $attempt = 0;
        $progressAtFailure = null;

        while ($offset < $session->size) {
            try {
                if ($progressAtFailure !== null) {
                    // Find out what arrived of the failed request.
                    $offset = $this->offset($session);

                    if ($offset > $progressAtFailure) {
                        $attempt = 0;
                    }

                    $progressAtFailure = null;

                    continue;
                }

                $offset = $this->patch($session, $stream, $start, $offset);
                $attempt = 0;

                if ($onProgress !== null) {
                    $onProgress($offset, $session->size);
                }
            } catch (TransportException|ApiException $e) {
                if (!self::isRetryable($e) || !isset($this->retryDelays[$attempt])) {
                    throw $e;
                }

                $this->clock->sleep($this->retryDelays[$attempt]);
                ++$attempt;
                $progressAtFailure = $offset;
            }
        }

        return $session->withOffset($offset);
    }

    /**
     * Send the chunk at the offset.
     *
     * @param int<0, max> $start
     * @param int<0, max> $offset
     *
     * @return int<0, max> The offset the server reports afterwards.
     */
    private function patch(TusSession $session, Stream $stream, int $start, int $offset): int
    {
        $length = min($this->chunkSize, $session->size - $offset);
        $headers = [
            'Upload-Offset' => (string) $offset,
            'Content-Type' => 'application/offset+octet-stream',
        ];

        if ($this->checksums) {
            $headers['Upload-Checksum'] = 'sha1 ' . base64_encode($this->sha1($stream, $start + $offset, $length));
        }

        self::seek($stream, $start + $offset);
        $response = $this->send(Method::Patch, $session->url, $session->videoId, $session->expires, $headers, new Stream($stream->resource, $length), expected: 204);
        $next = self::offsetHeader($response);

        if ($next <= $offset || $next > $session->size) {
            throw new SerializationException("The TUS server reported the offset {$next} after a chunk sent at {$offset}.");
        }

        return $next;
    }

    /**
     * The offset the server reports for an upload.
     *
     * @return int<0, max>
     */
    private function offset(TusSession $session): int
    {
        $offset = self::offsetHeader($this->send(Method::Head, $session->url, $session->videoId, $session->expires));

        if ($offset > $session->size) {
            throw new SerializationException("The TUS server reported the offset {$offset} for an upload of {$session->size} bytes.");
        }

        return $offset;
    }

    /**
     * Send one request of the TUS protocol.
     *
     * @param non-empty-string      $uri
     * @param array<string, string> $headers
     * @param int|null              $expected The success status; any 2xx if null.
     */
    private function send(
        Method $method,
        string $uri,
        string $videoId,
        DateTimeInterface $expires,
        array $headers = [],
        ?Stream $body = null,
        ?int $expected = null,
    ): Response {
        $request = new Request(
            method: $method,
            uri: $uri,
            headers: [
                'Tus-Resumable' => self::PROTOCOL,
                'User-Agent' => $this->options->userAgent,
                // Checked on every request, so every request carries the signature.
                ...TusUpload::presign($this->libraryId, $this->apiKey, $videoId, $expires)->headers,
                ...$headers,
            ],
            body: $body,
            timeout: $body === null ? $this->options->timeout : $this->options->transferTimeout,
        );

        $this->rateLimiter->acquire();
        $response = $this->httpClient->send($request);

        if (!$response->isSuccessful || ($expected !== null && $response->statusCode !== $expected)) {
            throw ApiException::fromResponse($response, $request);
        }

        return $response;
    }

    /**
     * Run a single request, repeating it after the failures worth another try.
     *
     * @template T
     *
     * @param Closure(): T $request
     *
     * @return T
     */
    private function retrying(Closure $request): mixed
    {
        $attempt = 0;

        while (true) {
            try {
                return $request();
            } catch (TransportException|ApiException $e) {
                if (!self::isRetryable($e) || !isset($this->retryDelays[$attempt])) {
                    throw $e;
                }

                $this->clock->sleep($this->retryDelays[$attempt]);
                ++$attempt;
            }
        }
    }

    /**
     * Network failures and server errors pass, as do an offset conflict (409),
     * a locked upload (423), a rate limit (429) and a corrupted chunk (460);
     * other client errors, such as an expired signature (401) or an upload that
     * is gone (404), would fail again.
     */
    private static function isRetryable(TransportException|ApiException $e): bool
    {
        return $e instanceof TransportException
            || $e->statusCode >= 500
            || \in_array($e->statusCode, [409, 423, 429, 460], true);
    }

    /**
     * The SHA-1 digest of a part of the stream.
     *
     * @param int<0, max> $position
     */
    private function sha1(Stream $stream, int $position, int $length): string
    {
        self::seek($stream, $position);
        $context = hash_init('sha1');

        while ($length > 0) {
            $read = hash_update_stream($context, $stream->resource, min(self::HASH_BUFFER, $length));

            if ($read < 1) {
                throw new InvalidArgumentException('The file ended before its announced size.');
            }

            $length -= $read;
        }

        return hash_final($context, true);
    }

    /**
     * @return int<0, max>
     */
    private static function offsetHeader(Response $response): int
    {
        $value = $response->header('upload-offset');
        $offset = filter_var($value, \FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

        if ($offset === false) {
            throw new SerializationException("The TUS server sent no valid Upload-Offset, got \"{$value}\".");
        }

        return $offset;
    }

    /**
     * Resolve the location of a new upload against the endpoint.
     *
     * @return non-empty-string
     */
    private function resolve(string $location): string
    {
        if (preg_match('~^https?://~i', $location) === 1) {
            return $location;
        }

        $parts = parse_url($this->endpoint);
        $scheme = \is_array($parts) ? ($parts['scheme'] ?? 'https') : 'https';
        $host = \is_array($parts) ? ($parts['host'] ?? '') : '';
        $port = \is_array($parts) && isset($parts['port']) ? ":{$parts['port']}" : '';

        if (str_starts_with($location, '/')) {
            return "{$scheme}://{$host}{$port}{$location}";
        }

        $directory = rtrim($this->endpoint, '/');

        return "{$directory}/{$location}";
    }

    private function assertOwn(TusSession $session): void
    {
        if ($session->libraryId !== $this->libraryId) {
            throw new InvalidArgumentException("The upload belongs to the video library {$session->libraryId}, not {$this->libraryId}.");
        }
    }

    private static function seekable(string|Stream $file): Stream
    {
        $stream = \is_string($file) ? Stream::fromString($file) : $file;

        if (!$stream->isSeekable) {
            throw new InvalidArgumentException('The file must be seekable to be uploaded in chunks, e.g. opened with Stream::fromFile().');
        }

        return $stream;
    }

    /**
     * @param int<0, max> $position
     */
    private static function seek(Stream $stream, int $position): void
    {
        if (fseek($stream->resource, $position) !== 0) {
            throw new InvalidArgumentException("Unable to seek to byte {$position} of the file.");
        }
    }
}
