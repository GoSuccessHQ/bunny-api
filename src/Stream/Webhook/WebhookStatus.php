<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Stream\Webhook;

/**
 * The video states a Stream webhook reports.
 *
 * These codes differ from the `status` of a video returned by the API.
 */
enum WebhookStatus: int
{
    /** The video has been queued for encoding. */
    case Queued = 0;

    /** Processing of the preview and format details has begun. */
    case Processing = 1;

    /** The video is encoding. */
    case Encoding = 2;

    /** Encoding has finished and the video is fully available. */
    case Finished = 3;

    /** One resolution has finished; the first one also makes the video playable. */
    case ResolutionFinished = 4;

    /** Encoding failed; processing has ended. */
    case Failed = 5;

    /** A presigned (TUS) upload has started. */
    case PresignedUploadStarted = 6;

    /** A presigned (TUS) upload has finished. */
    case PresignedUploadFinished = 7;

    /** A presigned (TUS) upload has failed. */
    case PresignedUploadFailed = 8;

    /** Captions were generated automatically. */
    case CaptionsGenerated = 9;

    /** The title or description was generated automatically. */
    case TitleOrDescriptionGenerated = 10;
}
