<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Http;

use InvalidArgumentException;
use RuntimeException;

/**
 * A PHP stream used as a request body or as the target of a response body.
 *
 * Streams let large uploads and downloads (Edge Storage files, videos) pass
 * through without being loaded into memory.
 */
final class Stream
{
    /**
     * The underlying stream resource.
     *
     * @var resource
     */
    public readonly mixed $resource;

    /**
     * The number of bytes that will be read from the current position, if known.
     */
    public readonly ?int $size;

    /**
     * Whether the stream supports seeking, which is required to rewind it
     * before a retry or to compute a checksum without consuming it.
     */
    public bool $isSeekable {
        get => stream_get_meta_data($this->resource)['seekable'];
    }

    /**
     * @param resource $resource An open stream resource.
     * @param int|null $size     Number of bytes to transfer; detected from the
     *                           stream when omitted and the stream allows it.
     */
    public function __construct(mixed $resource, ?int $size = null)
    {
        if (!\is_resource($resource) || get_resource_type($resource) !== 'stream') {
            throw new InvalidArgumentException('Expected an open stream resource.');
        }

        if ($size !== null && $size < 0) {
            throw new InvalidArgumentException('The stream size must not be negative.');
        }

        $this->resource = $resource;
        $this->size = $size ?? self::detectSize($resource);
    }

    /**
     * Open a local file, e.g. `Stream::fromFile('video.mp4')` for an upload or
     * `Stream::fromFile('backup.zip', 'wb')` as a download target.
     */
    public static function fromFile(string $path, string $mode = 'rb'): self
    {
        $resource = @fopen($path, $mode);

        if ($resource === false) {
            throw new RuntimeException("Unable to open {$path} with mode {$mode}.");
        }

        return new self($resource);
    }

    /**
     * Wrap in-memory contents; spills to a temporary file above 2 MiB.
     */
    public static function fromString(string $contents): self
    {
        $stream = self::temporary();
        fwrite($stream->resource, $contents);
        rewind($stream->resource);

        return new self($stream->resource);
    }

    /**
     * Create an empty, seekable temporary stream (memory first, then disk).
     */
    public static function temporary(): self
    {
        $resource = fopen('php://temp/maxmemory:2097152', 'w+b');

        if ($resource === false) {
            throw new RuntimeException('Unable to open a temporary stream.');
        }

        return new self($resource);
    }

    /**
     * The current read/write position, or null for streams that do not track it.
     *
     * @return int<0, max>|null
     */
    public function position(): ?int
    {
        $position = ftell($this->resource);

        return $position === false || $position < 0 ? null : $position;
    }

    /**
     * Read the remaining contents into a string.
     */
    public function contents(): string
    {
        $contents = stream_get_contents($this->resource);

        if ($contents === false) {
            throw new RuntimeException('Unable to read from the stream.');
        }

        return $contents;
    }

    /**
     * Only regular files (including php://temp and php://memory) report a
     * reliable size; pipes and sockets report 0, which would be wrong.
     *
     * @param resource $resource
     */
    private static function detectSize(mixed $resource): ?int
    {
        $stat = fstat($resource);
        $position = ftell($resource);

        if ($stat === false || $position === false || ($stat['mode'] & 0o170000) !== 0o100000) {
            return null;
        }

        $size = $stat['size'] - $position;

        return $size >= 0 ? $size : null;
    }
}
