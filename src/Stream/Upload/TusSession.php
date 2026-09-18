<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Stream\Upload;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * A resumable upload: where it lives on the server and how far it got.
 *
 * Store it with toArray() to resume the upload later, even from another
 * process, with {@see TusUploader::resume()}.
 */
final readonly class TusSession
{
    /**
     * The URL of the upload on the TUS server.
     *
     * @var non-empty-string
     */
    public string $url;

    /**
     * The size of the file in bytes.
     *
     * @var int<0, max>
     */
    public int $size;

    /**
     * The number of bytes the server has received.
     *
     * @var int<0, max>
     */
    public int $offset;

    /**
     * @param string            $url       The URL of the upload on the TUS server.
     * @param int               $libraryId The ID of the video library.
     * @param string            $videoId   The GUID of the video the file belongs to.
     * @param int               $size      The size of the file in bytes.
     * @param int               $offset    The number of bytes the server has received.
     * @param DateTimeImmutable $expires   Until when the upload is authorized; it has to be
     *                                     finished by then.
     */
    public function __construct(
        string $url,
        public int $libraryId,
        public string $videoId,
        int $size,
        int $offset,
        public DateTimeImmutable $expires,
    ) {
        if ($url === '') {
            throw new InvalidArgumentException('The upload URL must not be empty.');
        }

        if ($size < 0 || $offset < 0 || $offset > $size) {
            throw new InvalidArgumentException("The offset {$offset} does not lie within the size {$size}.");
        }

        $this->url = $url;
        $this->size = $size;
        $this->offset = $offset;
    }

    /**
     * Whether the server has received the whole file.
     */
    public function isComplete(): bool
    {
        return $this->offset === $this->size;
    }

    /**
     * The session with the given number of received bytes.
     */
    public function withOffset(int $offset): self
    {
        return new self($this->url, $this->libraryId, $this->videoId, $this->size, $offset, $this->expires);
    }

    /**
     * @return array{url: string, libraryId: int, videoId: string, size: int, offset: int, expires: int}
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'libraryId' => $this->libraryId,
            'videoId' => $this->videoId,
            'size' => $this->size,
            'offset' => $this->offset,
            'expires' => $this->expires->getTimestamp(),
        ];
    }

    /**
     * Restore a session stored with toArray().
     *
     * @param array<array-key, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $url = $data['url'] ?? null;
        $libraryId = $data['libraryId'] ?? null;
        $videoId = $data['videoId'] ?? null;
        $size = $data['size'] ?? null;
        $offset = $data['offset'] ?? null;
        $expires = $data['expires'] ?? null;

        if (!\is_string($url) || !\is_int($libraryId) || !\is_string($videoId) || !\is_int($size) || !\is_int($offset) || !\is_int($expires)) {
            throw new InvalidArgumentException('Expected the array of TusSession::toArray().');
        }

        return new self($url, $libraryId, $videoId, $size, $offset, new DateTimeImmutable("@{$expires}"));
    }
}
