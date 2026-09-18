<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Storage;

use GoSuccess\Bunny\ClientOptions;
use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Http\Connection;
use GoSuccess\Bunny\Http\CurlHttpClient;
use GoSuccess\Bunny\Http\HttpClient;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\RateLimit\NullRateLimiter;
use GoSuccess\Bunny\RateLimit\RateLimiter;
use GoSuccess\Bunny\Storage\Model\StorageObject;
use InvalidArgumentException;
use SensitiveParameter;

/**
 * Client for the bunny.net Edge Storage API of one storage zone.
 *
 * Paths are relative to the zone root, e.g. `images/logo.png`; directories
 * may end with a slash. Uploads and downloads accept streams, so files never
 * have to fit into memory, and are subject to
 * {@see ClientOptions::$transferTimeout} instead of the regular timeout.
 *
 * Authenticates with the storage zone's password; its read-only password
 * works for listing and downloading.
 */
final class StorageClient
{
    private readonly Connection $connection;

    /**
     * @param string          $zone        The name of the storage zone.
     * @param string          $password    The zone's password or read-only password (dashboard → storage zone → FTP & API access).
     * @param StorageRegion   $region      The zone's primary region, which determines the API host.
     * @param ClientOptions   $options     Timeouts, retries and user agent.
     * @param HttpClient|null $httpClient  Custom transport; defaults to the built-in cURL transport.
     * @param RateLimiter     $rateLimiter Client-side throttling; disabled by default.
     * @param string|null     $baseUri     Overrides the region's host, e.g. with the zone's storage hostname.
     */
    public function __construct(
        public readonly string $zone,
        #[SensitiveParameter]
        string $password,
        StorageRegion $region = StorageRegion::Falkenstein,
        ClientOptions $options = new ClientOptions(),
        ?HttpClient $httpClient = null,
        RateLimiter $rateLimiter = new NullRateLimiter(),
        ?string $baseUri = null,
    ) {
        if (trim($zone) === '' || str_contains($zone, '/')) {
            throw new InvalidArgumentException("Invalid storage zone name \"{$zone}\".");
        }

        $this->connection = new Connection(
            $baseUri ?? "https://{$region->host()}",
            $password,
            $options,
            $httpClient ?? new CurlHttpClient($options->timeout, $options->connectTimeout),
            $rateLimiter,
        );
    }

    /**
     * List the files and directories in a directory.
     *
     * `GET /{storageZoneName}/{path}/`
     *
     * @param string $directory The directory, relative to the zone root; empty for the root.
     *
     * @return list<StorageObject>
     */
    public function list(string $directory = ''): array
    {
        $data = $this->connection->json(Method::Get, $this->path($directory, directory: true));

        if (!\is_array($data) || !array_is_list($data)) {
            throw new SerializationException('Expected a JSON array of storage objects.');
        }

        return Cast::modelList(StorageObject::class, $data);
    }

    /**
     * Get the metadata of a file or directory without downloading it.
     *
     * Uses the `DESCRIBE` method, which the specification does not document
     * but bunny.net's own CLI uses.
     *
     * `DESCRIBE /{storageZoneName}/{path}`
     *
     * @param string $path The file or directory, relative to the zone root.
     */
    public function describe(string $path): StorageObject
    {
        $data = $this->connection->json(Method::Describe, $this->path($path));

        if (!\is_array($data)) {
            throw new SerializationException('Expected a JSON object describing the storage object.');
        }

        return StorageObject::fromArray($data);
    }

    /**
     * Whether a file or directory exists.
     *
     * `DESCRIBE /{storageZoneName}/{path}`
     *
     * @param string $path The file or directory, relative to the zone root.
     */
    public function exists(string $path): bool
    {
        try {
            $this->describe($path);

            return true;
        } catch (NotFoundException) {
            return false;
        }
    }

    /**
     * Download a file into memory. For large files, use download().
     *
     * `GET /{storageZoneName}/{path}/{fileName}`
     *
     * @param string $path The file, relative to the zone root.
     *
     * @return string The file contents.
     */
    public function get(string $path): string
    {
        $buffer = Stream::temporary();
        $this->download($path, $buffer);
        rewind($buffer->resource);

        return $buffer->contents();
    }

    /**
     * Download a file into a stream, e.g. `Stream::fromFile('backup.zip', 'wb')`.
     *
     * The file is written from the stream's current position. If the transfer
     * fails half-way and the stream is seekable, it is truncated before the
     * retry, so the target never holds a mix of two attempts.
     *
     * `GET /{storageZoneName}/{path}/{fileName}`
     *
     * @param string $path   The file, relative to the zone root.
     * @param Stream $target Where to write the file.
     */
    public function download(string $path, Stream $target): void
    {
        $this->connection->send(Method::Get, $this->path($path), headers: ['Accept' => '*/*'], sink: $target);
    }

    /**
     * Upload a file. Missing directories are created; an existing file is replaced.
     *
     * `PUT /{storageZoneName}/{path}/{fileName}`
     *
     * @param string        $path           The file, relative to the zone root.
     * @param string|Stream $contents       The file contents, e.g. `Stream::fromFile('video.mp4')`.
     * @param string|null   $contentType    The content type to serve the file with. By default the
     *                                      CDN derives it from the file extension.
     * @param bool          $verifyChecksum Send the SHA-256 checksum, so bunny.net rejects a corrupted
     *                                      upload (HTTP 400). Reads the contents one extra time; a
     *                                      non-seekable stream is buffered in a temporary file first.
     */
    public function upload(string $path, string|Stream $contents, ?string $contentType = null, bool $verifyChecksum = true): void
    {
        $headers = ['Content-Type' => 'application/octet-stream'];

        if ($contentType !== null) {
            // Not in the specification; bunny.net's own CLI sets it.
            $headers['Override-Content-Type'] = $contentType;
        }

        if ($verifyChecksum) {
            [$contents, $headers['Checksum']] = self::checksum($contents);
        }

        $this->connection->send(Method::Put, $this->path($path), body: $contents, headers: $headers);
    }

    /**
     * Create a directory, including missing parents.
     *
     * `PUT /{storageZoneName}/{path}/`
     *
     * @param string $path The directory, relative to the zone root.
     */
    public function createDirectory(string $path): void
    {
        $this->connection->send(Method::Put, $this->path($path, directory: true, allowRoot: false));
    }

    /**
     * Delete a file, or a directory with everything in it when the path ends
     * with a slash.
     *
     * `DELETE /{storageZoneName}/{path}/{fileName}`
     *
     * @param string $path The file or directory, relative to the zone root.
     */
    public function delete(string $path): void
    {
        $this->connection->send(Method::Delete, $this->path($path, allowRoot: false));
    }

    /**
     * Delete a directory with everything in it.
     *
     * `DELETE /{storageZoneName}/{path}/`
     *
     * @param string $path      The directory, relative to the zone root.
     * @param bool   $allowRoot Set to true to delete the root, i.e. every file of the zone.
     */
    public function deleteDirectory(string $path, bool $allowRoot = false): void
    {
        $query = $allowRoot && self::isRoot($path) ? ['allowRootDelete' => 'true'] : [];

        $this->connection->send(Method::Delete, $this->path($path, directory: true, allowRoot: $allowRoot), $query);
    }

    /**
     * Hide the password from var_dump() and print_r().
     *
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return ['zone' => $this->zone, 'baseUri' => $this->connection->baseUri];
    }

    /**
     * The URL path of an object: the zone name followed by the encoded segments.
     */
    private function path(string $path, bool $directory = false, bool $allowRoot = true): string
    {
        $relative = trim($path, '/');

        if (!$allowRoot && $relative === '') {
            throw new InvalidArgumentException('This would affect the root of the storage zone; use deleteDirectory() with allowRoot: true to delete everything.');
        }

        $segments = $relative === '' ? [] : explode('/', $relative);

        foreach ($segments as $segment) {
            if ($segment === '.' || $segment === '..') {
                throw new InvalidArgumentException("Relative segments are not allowed in storage paths: \"{$path}\".");
            }
        }

        $encoded = implode('/', array_map(rawurlencode(...), $segments));
        $isDirectory = $directory || str_ends_with($path, '/');

        return rawurlencode($this->zone) . '/' . $encoded . ($isDirectory && $encoded !== '' ? '/' : '');
    }

    private static function isRoot(string $path): bool
    {
        return trim($path, '/') === '';
    }

    /**
     * Compute the upper-case hex SHA-256 checksum without consuming the content.
     *
     * @return array{string|Stream, string}
     */
    private static function checksum(string|Stream $contents): array
    {
        if (\is_string($contents)) {
            return [$contents, strtoupper(hash('sha256', $contents))];
        }

        if (!$contents->isSeekable || $contents->position() === null) {
            $buffer = Stream::temporary();
            stream_copy_to_stream($contents->resource, $buffer->resource);
            rewind($buffer->resource);
            $contents = new Stream($buffer->resource);
        }

        $start = $contents->position() ?? 0;
        $context = hash_init('sha256');
        hash_update_stream($context, $contents->resource);
        fseek($contents->resource, $start);

        return [$contents, strtoupper(hash_final($context))];
    }
}
