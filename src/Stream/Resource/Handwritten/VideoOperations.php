<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Stream\Resource\Handwritten;

use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Model\Json;
use GoSuccess\Bunny\Stream\Model\CaptionValidation;
use GoSuccess\Bunny\Stream\Resource\VideoResource;
use InvalidArgumentException;

/**
 * Hand-written methods of {@see VideoResource}.
 */
trait VideoOperations
{
    /**
     * Upload the file of a video created with create().
     *
     * The file is sent as the raw request body and streamed, so it does not
     * have to fit into memory. The HTTP API cannot resume an interrupted upload;
     * for files over 2 GB or unstable connections, bunny.net recommends TUS
     * resumable uploads (see {@see \GoSuccess\Bunny\Stream\Upload\TusUpload}).
     *
     * `PUT /library/{libraryId}/videos/{videoId}`
     *
     * @param string            $videoId             The GUID of the video.
     * @param string|Stream     $file                The video file, e.g. `Stream::fromFile('video.mp4')`.
     * @param bool|null         $jitEnabled          Enable JIT encoding for this video (requires Premium Encoding); overrides the library settings.
     * @param list<string>|null $enabledResolutions  Resolutions to encode, e.g. `['720p', '1080p']`; overrides the library settings.
     * @param list<string>|null $enabledOutputCodecs Codecs to encode with, e.g. `['x264', 'vp9']`; overrides the library settings.
     * @param bool|null         $transcribeEnabled   Transcribe the video; this incurs transcription charges.
     * @param list<string>|null $transcribeLanguages Target languages of the transcription as ISO 639-1 codes.
     * @param string|null       $sourceLanguage      The language spoken in the video as ISO 639-1 code.
     * @param bool|null         $generateTitle       Generate the title from the transcription.
     * @param bool|null         $generateDescription Generate the description from the transcription.
     * @param bool|null         $generateChapters    Generate chapters from the transcription.
     * @param bool|null         $generateMoments     Generate moments from the transcription.
     */
    public function upload(
        string $videoId,
        string|Stream $file,
        ?bool $jitEnabled = null,
        ?array $enabledResolutions = null,
        ?array $enabledOutputCodecs = null,
        ?bool $transcribeEnabled = null,
        ?array $transcribeLanguages = null,
        ?string $sourceLanguage = null,
        ?bool $generateTitle = null,
        ?bool $generateDescription = null,
        ?bool $generateChapters = null,
        ?bool $generateMoments = null,
    ): void {
        $this->connection->send(
            Method::Put,
            "library/{$this->libraryId}/videos/{$this->segment($videoId)}",
            [
                'jitEnabled' => $jitEnabled,
                'enabledResolutions' => self::commaList($enabledResolutions),
                'enabledOutputCodecs' => self::commaList($enabledOutputCodecs),
                'transcribeEnabled' => $transcribeEnabled,
                'transcribeLanguages' => self::commaList($transcribeLanguages),
                'sourceLanguage' => $sourceLanguage,
                'generateTitle' => $generateTitle,
                'generateDescription' => $generateDescription,
                'generateChapters' => $generateChapters,
                'generateMoments' => $generateMoments,
            ],
            body: $file,
            headers: ['Content-Type' => 'application/octet-stream'],
        );
    }

    /**
     * Create a video from a URL: bunny.net downloads and encodes the file.
     *
     * The specification documents a bare status response, but the API also
     * returns the GUID of the new video, which bunny.net's own CLI relies on.
     *
     * `POST /library/{libraryId}/videos/fetch`
     *
     * @param string                     $url           The URL to download the video from.
     * @param string|null                $title         The title of the video; defaults to the file name in the URL.
     * @param string|null                $collectionId  The ID of the collection to put the video in.
     * @param int|null                   $thumbnailTime The video time in milliseconds to take the thumbnail from.
     * @param array<string, string>|null $headers       Headers to send with the download, e.g. to authenticate at the source.
     *
     * @return string|null The GUID of the new video, or null if the response lacks it.
     */
    public function fetch(
        string $url,
        ?string $title = null,
        ?string $collectionId = null,
        ?int $thumbnailTime = null,
        ?array $headers = null,
    ): ?string {
        $body = ['url' => $url];

        if ($title !== null) {
            $body['title'] = $title;
        }

        if ($headers !== null) {
            $body['headers'] = Json::map($headers);
        }

        $data = $this->connection->json(
            Method::Post,
            "library/{$this->libraryId}/videos/fetch",
            ['collectionId' => $collectionId, 'thumbnailTime' => $thumbnailTime],
            $body,
        );
        $id = \is_array($data) ? Cast::string($data['id'] ?? null) : null;

        return $id === '' ? null : $id;
    }

    /**
     * Set the thumbnail to an image bunny.net fetches from a URL.
     *
     * `POST /library/{libraryId}/videos/{videoId}/thumbnail`
     *
     * @param string $videoId The GUID of the video.
     * @param string $url     A publicly reachable image URL.
     */
    public function setThumbnail(string $videoId, string $url): void
    {
        $this->thumbnail($videoId, ['thumbnailUrl' => $url]);
    }

    /**
     * Use one of the five thumbnails generated while encoding.
     *
     * `POST /library/{libraryId}/videos/{videoId}/thumbnail`
     *
     * @param string $videoId The GUID of the video.
     * @param int    $number  The generated thumbnail, 1 to 5.
     */
    public function useGeneratedThumbnail(string $videoId, int $number): void
    {
        if ($number < 1 || $number > 5) {
            throw new InvalidArgumentException('Generated thumbnails are numbered 1 to 5.');
        }

        $this->thumbnail($videoId, ['thumbnailUrl' => "thumbnail_{$number}.jpg"]);
    }

    /**
     * Upload the thumbnail image.
     *
     * `POST /library/{libraryId}/videos/{videoId}/thumbnail`
     *
     * @param string        $videoId     The GUID of the video.
     * @param string|Stream $image       The image file.
     * @param string        $contentType The image's media type.
     */
    public function uploadThumbnail(string $videoId, string|Stream $image, string $contentType = 'image/jpeg'): void
    {
        $this->thumbnail($videoId, [], $image, $contentType);
    }

    /**
     * Add or replace the captions of one language.
     *
     * The API expects the captions file base64-encoded; this method encodes it.
     *
     * `POST /library/{libraryId}/videos/{videoId}/captions/{srclang}`
     *
     * @param string        $videoId  The GUID of the video.
     * @param string        $language The language code of the captions, e.g. `en`.
     * @param string|Stream $captions The captions file, e.g. WebVTT.
     * @param string|null   $label    The label the player shows, e.g. `English`.
     *
     * @return CaptionValidation|null The validation result, when the API reports one.
     */
    public function addCaption(string $videoId, string $language, string|Stream $captions, ?string $label = null): ?CaptionValidation
    {
        $body = [
            'srclang' => $language,
            'captionsFile' => base64_encode($captions instanceof Stream ? $captions->contents() : $captions),
        ];

        if ($label !== null) {
            $body['label'] = $label;
        }

        $data = $this->connection->json(
            Method::Post,
            "library/{$this->libraryId}/videos/{$this->segment($videoId)}/captions/{$this->segment($language)}",
            body: $body,
        );

        return Cast::model(CaptionValidation::class, \is_array($data) ? ($data['data'] ?? null) : null);
    }

    /**
     * Get the raw heatmap data the player shows on its timeline.
     *
     * The format is not documented; an empty string means the library has
     * heatmaps disabled or there is no data yet.
     *
     * `GET /library/{libraryId}/videos/{videoId}/play/heatmap`
     *
     * @param string      $videoId The GUID of the video.
     * @param string|null $token   The embed view token, if token authentication is enabled.
     * @param int|null    $expires The expiry of the token as Unix timestamp.
     */
    public function playHeatmap(string $videoId, ?string $token = null, ?int $expires = null): string
    {
        return $this->connection->send(
            Method::Get,
            "library/{$this->libraryId}/videos/{$this->segment($videoId)}/play/heatmap",
            ['token' => $token, 'expires' => $expires],
        )->body;
    }

    /**
     * @param array<string, string> $query
     */
    private function thumbnail(string $videoId, array $query, string|Stream|null $image = null, ?string $contentType = null): void
    {
        $this->connection->send(
            Method::Post,
            "library/{$this->libraryId}/videos/{$this->segment($videoId)}/thumbnail",
            $query,
            body: $image,
            headers: $contentType === null ? [] : ['Content-Type' => $contentType],
        );
    }

    /**
     * @param list<string>|null $values
     */
    private static function commaList(?array $values): ?string
    {
        return $values === null ? null : implode(',', $values);
    }
}
