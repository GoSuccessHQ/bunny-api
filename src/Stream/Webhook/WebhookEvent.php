<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Stream\Webhook;

use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Model\Cast;
use JsonException;

/**
 * The notification Bunny Stream sends to a webhook when a video changes state.
 */
final readonly class WebhookEvent
{
    /**
     * @param int                $libraryId  The ID of the video library.
     * @param string             $videoId    The GUID of the video.
     * @param WebhookStatus|null $status     The new state, or null for a code this client does not know yet.
     * @param int                $statusCode The raw state code.
     */
    public function __construct(
        public int $libraryId,
        public string $videoId,
        public ?WebhookStatus $status,
        public int $statusCode,
    ) {}

    /**
     * Parse the raw request body of a webhook call.
     *
     * Use {@see WebhookSignature::parse()} to verify the signature at the same time.
     */
    public static function fromJson(string $body): self
    {
        try {
            $data = json_decode($body, true, 16, \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new SerializationException("Invalid webhook body: {$e->getMessage()}", 0, $e);
        }

        $libraryId = \is_array($data) ? Cast::int($data['VideoLibraryId'] ?? null) : null;
        $videoId = \is_array($data) ? Cast::string($data['VideoGuid'] ?? null) : null;
        $statusCode = \is_array($data) ? Cast::int($data['Status'] ?? null) : null;

        if ($libraryId === null || $videoId === null || $statusCode === null) {
            throw new SerializationException('The webhook body lacks VideoLibraryId, VideoGuid or Status.');
        }

        return new self($libraryId, $videoId, WebhookStatus::tryFrom($statusCode), $statusCode);
    }
}
