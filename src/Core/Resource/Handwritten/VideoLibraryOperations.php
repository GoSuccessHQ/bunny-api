<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Core\Resource\Handwritten;

use GoSuccess\Bunny\Core\Resource\VideoLibraryResource;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Stream;

/**
 * Hand-written methods of {@see VideoLibraryResource}.
 *
 * bunny.net does not document the request body of the three image uploads. The
 * image is sent as the raw request body, the format every other bunny.net
 * upload endpoint uses. An image that is too large is rejected with HTTP 413.
 */
trait VideoLibraryOperations
{
    /**
     * Upload the watermark image of a video library.
     *
     * `PUT /videolibrary/{id}/watermark`
     *
     * @param int           $id          The ID of the video library
     * @param string|Stream $image       The image file.
     * @param string        $contentType The image's media type.
     */
    public function addWatermark(int $id, string|Stream $image, string $contentType = 'image/png'): void
    {
        $this->uploadImage("videolibrary/{$id}/watermark", $image, $contentType);
    }

    /**
     * Upload the thumbnail shown for live streams of a video library.
     *
     * `PUT /videolibrary/{id}/live/thumbnail`
     *
     * @param int           $id          The ID of the video library
     * @param string|Stream $image       The image file.
     * @param string        $contentType The image's media type.
     */
    public function addLiveThumbnail(int $id, string|Stream $image, string $contentType = 'image/png'): void
    {
        $this->uploadImage("videolibrary/{$id}/live/thumbnail", $image, $contentType);
    }

    /**
     * Upload the watermark image of live streams of a video library.
     *
     * `PUT /videolibrary/{id}/live/watermark`
     *
     * @param int           $id          The ID of the video library
     * @param string|Stream $image       The image file.
     * @param string        $contentType The image's media type.
     */
    public function addLiveWatermark(int $id, string|Stream $image, string $contentType = 'image/png'): void
    {
        $this->uploadImage("videolibrary/{$id}/live/watermark", $image, $contentType);
    }

    private function uploadImage(string $path, string|Stream $image, string $contentType): void
    {
        $this->connection->send(Method::Put, $path, body: $image, headers: ['Content-Type' => $contentType]);
    }
}
